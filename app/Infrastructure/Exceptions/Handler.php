<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Exceptions;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAware;
use Cadexsa\Domain\Exceptions\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Cadexsa\Domain\Exceptions\AuthenticationException;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAwareTrait;
use Cadexsa\Presentation\Http\Exceptions\HttpException;
use Cadexsa\Presentation\Authorization\AuthorizationException;
use Cadexsa\Presentation\Http\Exceptions\NotFoundHttpException;
use Cadexsa\Presentation\Http\Exceptions\TokenMismatchException;
use Cadexsa\Presentation\Http\Exceptions\AccessDeniedHttpException;

class Handler implements HttpMessageFactoriesAware
{
    use HttpMessageFactoriesAwareTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected array $dontReport = [];

    /**
     * A list of the internal exception types that should not be reported.
     *
     * @var string[]
     */
    protected array $internalDontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        HttpResponseException::class,
        TokenMismatchException::class,
    ];

    /**
     * Indicate that the given exception type should not be reported.
     */
    protected function ignore(string $class)
    {
        $this->dontReport[] = $class;

        return $this;
    }

    /**
     * Report or log an exception.
     *
     * @throws \Throwable
     */
    public function report(\Throwable $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        $logger = app()->getLogger();

        switch (true) {
            case ($e instanceof \Error):
            case ($e instanceof \LogicException):
                $logger->critical('{exception}', ['exception' => $e]);
                break;

            case ($e instanceof \RuntimeException):
                $logger->error('{exception}', ['exception' => $e]);
                break;
        }
    }

    /**
     * Determine if the exception should be reported.
     *
     * @return bool
     */
    public function shouldReport(\Throwable $e)
    {
        return !$this->shouldntReport($e);
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @return bool
     */
    protected function shouldntReport(\Throwable $e)
    {
        $dontReport = array_merge($this->dontReport, $this->internalDontReport);

        return in_array(get_class($e), $dontReport);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @throws \Throwable
     */
    public function render(\Throwable $e): ResponseInterface
    {
        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($e);
        }

        return $this->prepareResponse($e);
    }


    /**
     * Prepare exception for rendering.
     * 
     * @return \Throwable
     */
    protected function prepareException(\Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof TokenMismatchException) {
            $e = new HttpException($e->getMessage(), 419, $e);
        }

        return $e;
    }

    /**
     * Prepare a response for the given exception.
     */
    protected function prepareResponse(\Throwable $e): ResponseInterface
    {
        if ($e instanceof HttpException) {
            $e = new HttpException($e->getMessage(), 500);
        }

        return $this->convertExceptionToResponse($e);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated(AuthenticationException $e): ResponseInterface
    {
        return $this->responseFactory->createResponse($e->getCode() ?? 301)
            ->withHeader('Location', $e->redirectTo() ?? route('login'));
    }

    /**
     * Create a response for the given exception.
     */
    protected function convertExceptionToResponse(\Throwable $e): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(
            $e instanceof HttpException ? $e->getStatusCode() : 500
        )->withBody($this->renderExceptionContent($e));

        if ($e instanceof HttpException) {
            foreach ($e->getHeaders() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
        }

        return $response;
    }

    /**
     * Get the response content for the given exception.
     */
    protected function renderExceptionContent(\Throwable $e): StreamInterface
    {
        return $this->streamFactory->createStream($e->getMessage());
    }
}
