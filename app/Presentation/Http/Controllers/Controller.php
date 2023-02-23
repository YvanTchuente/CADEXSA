<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\ServiceRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAware;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAwareTrait;

abstract class Controller implements RequestHandlerInterface, HttpMessageFactoriesAware
{
    use HttpMessageFactoriesAwareTrait;

    abstract public function handle(ServerRequestInterface $request): ResponseInterface;

    /**
     * Creates an HTTP response instance from the given view.
     * 
     * @param View $view The view.
     * @param int $code The response status code
     * @param string[] $headers A list of header values keyed by header name
     * @return ResponseInterface
     * 
     * @throws \LengthException
     */
    public function prepareResponseFromView(View $view, int $code = 200, array $headers = [])
    {
        $pageHeader = $this->pageHeader();
        $view->with('page_header', $pageHeader->render());

        return $this->prepareResponse($view->render(), $code, $headers);
    }

    /**
     * Creates an HTTP response instance from the given value.
     * 
     * @param string $response The response.
     * @param int $code The response status code
     * @param string[] $headers A list of header values keyed by header name
     * 
     * @return ResponseInterface
     * 
     * @throws \LengthException
     */
    protected function prepareResponse(string $response, int $code = 200, array $headers = [])
    {
        $body = $response;
        if (!$body) {
            throw new \LengthException('The response body is empty.');
        }

        $response = $this->responseFactory->createResponse($code);
        if ($headers) {
            foreach ($headers as $name => $values) {
                $response = $response->withHeader($name, $values);
            }
        }

        $body = $this->streamFactory->createStream($body);
        $response = $response->withBody($body);

        return $response;
    }

    /**
     * Obtain the rendered website page header view.
     * 
     * @return View
     */
    private function pageHeader()
    {
        $view_params = ['target' => app()->currentRequest()->getRequestTarget()];

        if (ServiceRegistry::authenticationService()->check()) {
            $exstudent = $_SESSION['exstudent'];
            $view_params += [
                'name' => $exstudent->getName(),
                'level' => $exstudent->getLevel(),
                'username' => $exstudent->getUsername(),
                'profilePicture' => $exstudent->getAvatar(),
                'pathToProfile' => sprintf("/exstudents/%s", strtolower($exstudent->getUsername()))
            ];
            $user_panel_view = new View(views_path("header/user_panel.php"), $view_params);
            $view_params['user_panel'] = $user_panel_view->render();
        }

        $header_view = new View(views_path("header/page_header.php"), $view_params ?? []);
        $navigation_menu_view = new View(views_path("header/navigation_menu_links.php"), $view_params);

        return $header_view->with('navigation_menu_links', $navigation_menu_view->render());
    }
}
