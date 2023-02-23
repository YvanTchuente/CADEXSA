<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Cadexsa\Presentation\Routing\Router;
use Psr\Http\Server\MiddlewareInterface;
use Cadexsa\Infrastructure\Messaging\Mailer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cadexsa\Infrastructure\Config\Repository;
use Cadexsa\Infrastructure\Exceptions\Handler;
use Cadexsa\Infrastructure\Logging\LogManager;
use Cadexsa\Infrastructure\Bootstrap\Bootstrapper;
use Cadexsa\Infrastructure\Persistence\IdGenerator;
use Cadexsa\Infrastructure\Persistence\DatabaseManager;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAware;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAwareTrait;
use Cadexsa\Infrastructure\Contracts\Mailer as MailerInterface;

class Application implements RequestHandlerInterface
{
    use HttpMessageFactoriesAwareTrait;

    /**
     * The application's globally acessible instance.
     */
    private static $instance;

    /**
     * The application's configuration settings.
     */
    private Repository $config;

    /**
     * The application's database manager.
     */
    public readonly DatabaseManager $database;

    /**
     * The application's router.
     */
    private Router $router;

    /**
     * The application's mailer.
     */
    private MailerInterface $mailer;

    /**
     * The application's logger.
     */
    private LoggerInterface $logger;

    /**
     * The base path of the application.
     */
    private string $basePath;

    /**
     * The application's HTTP middleware stack.
     *
     * @var MiddlewareInterface[]
     */
    private array $middleware = [];

    /**
     * The application's environment path.
     */
    private string $environmentPath;

    /**
     * The environment file to load during bootstrapping.
     */
    private string $environmentFile = '.env';

    /**
     * The plugin manager.
     */
    private PluginManager $plugins;

    /**
     * The bootstrap classes for the application.
     *
     * @var string[]
     */
    private array $bootstrappers = [
        \Cadexsa\Infrastructure\Bootstrap\LoadEnvironmentVariables::class,
        \Cadexsa\Infrastructure\Bootstrap\LoadConfiguration::class,
        \Cadexsa\Infrastructure\Bootstrap\HandleExceptions::class,
        \Cadexsa\Infrastructure\Bootstrap\RegisterFacades::class,
        \Cadexsa\Infrastructure\Bootstrap\LoadRoutes::class,
        \Cadexsa\Infrastructure\Bootstrap\RegisterMiddlewares::class,
    ];

    /**
     * Indicates if the application has been bootstrapped.
     */
    private bool $hasBeenBoostrapped = false;

    /**
     * The current request instance being handled.
     */
    private ?ServerRequestInterface $currentRequest = null;

    public function __construct(string $basePath = '')
    {
        static::setInstance($this);

        $this->basePath = $basePath;
        $this->config = new Repository;
        $this->database = new DatabaseManager($this);
        $this->router = new Router;
        $this->mailer = new Mailer;
        $this->logger = new LogManager($this);
        $this->plugins = new PluginManager;
    }

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public static function setInstance(Application $application)
    {
        static::$instance = $application;
    }

    /**
     * Gets the application's configuration settings.
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the application's configuration settings.
     */
    public function setConfig(Repository $config)
    {
        return $this->config = $config;
    }

    /**
     * Gets the application's router.
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Gets the application's mailer instance.
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Sets the application's mailer instance.
     */
    public function setMailer(MailerInterface $mailer)
    {
        return $this->mailer = $mailer;
    }

    /**
     * Gets the application's logger.
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the application's middleware stack.
     *
     * @param MiddlewareInterface[] $middleware
     */
    public function setMiddlewares(array $middleware)
    {
        array_walk($middleware, function ($middleware, $key) {
            if (!$middleware instanceof MiddlewareInterface) {
                throw new \InvalidArgumentException("$middleware is not a middleware.");
            }
        });
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * Gets the application's middleware stack.
     */
    public function middlewares()
    {
        return $this->middleware;
    }

    /**
     * Gets the application's ID generator.
     * 
     * @return IdGenerator
     */
    public function IdGenerator()
    {
        return $this->plugins->getPlugin('IdGenerator');
    }

    /**
     * Retrieves the plugin implementation of a given service.
     *
     * @param string $service The service name.
     * 
     * @return object
     * 
     * @throws \RuntimeException
     */
    public function getPlugin(string $name)
    {
        return $this->plugins->getPlugin($name);
    }

    /**
     * Determine if the application has been bootstrapped.
     */
    public function hasBeenBoostrapped()
    {
        return $this->hasBeenBoostrapped;
    }

    /**
     * Bootstraps the application.
     */
    public function bootstrap()
    {
        if (!$this->hasBeenBoostrapped) {

            foreach ($this->bootstrappers as $bootstrapper) {
                $bootstrapper = $this->makeBootstrapper($bootstrapper);
                $bootstrapper->bootstrap($this);
            }

            $this->hasBeenBoostrapped = true;
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->sendRequestThroughRouter($request);
        } catch (\Throwable $e) {
            $this->reportException($e);

            $response = $this->renderException($e);
        }

        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     */
    private function sendRequestThroughRouter(ServerRequestInterface $request): ResponseInterface
    {
        $this->currentRequest = $request;

        if ($this->middleware) {
            $middleware = array_shift($this->middleware);
            $response = $middleware->process($request, $this);
        } else {
            $response = $this->router->dispatch($request);
        }

        return $response;
    }

    /**
     * Sends a given HTTP response to the client.
     * 
     * @param ResponseInterface $response An HTTP response.
     */
    public function send(ResponseInterface $response)
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($response->getStatusCode());
        if ($response->getHeaders()) {
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }

        echo $response->getBody();
    }

    /**
     * Get the base path of the application.
     */
    public function basePath(string $path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the application configuration files.
     */
    public function configPath(string $path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the public directory.
     */
    public function publicPath(string $path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the views directory.
     */
    public function viewPath(string $path = '')
    {
        $basePath = $this->getConfig()->get('views.paths')[0];

        return rtrim($basePath, DIRECTORY_SEPARATOR) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the resources directory.
     */
    public function resourcePath(string $path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the storage directory.
     */
    public function storagePath(string $path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'storage' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the environment file directory.
     */
    public function environmentPath()
    {
        return $this->environmentPath ?? $this->basePath;
    }

    /**
     * Get the environment file the application is using.
     */
    public function environmentFile()
    {
        return $this->environmentFile ?? '.env';
    }

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param string $file The path to the file.
     */
    public function loadEnvironmentFrom(string $file)
    {
        if (file_exists($file)) {
            throw new \LogicException("This file does not exist.");
        }
        $this->environmentFile = $file;

        return $this;
    }

    /**
     * Get the current request instance being handled.
     */
    public function currentRequest()
    {
        return $this->currentRequest;
    }

    private function makeBootstrapper(string $bootstrapper): Bootstrapper
    {
        return new $bootstrapper;
    }

    /**
     * Report the exception to the exception handler.
     */
    private function reportException(\Throwable $e)
    {
        $this->getExceptionHandler()->report($e);
    }

    /**
     * Render the exception to a response.
     */
    private function renderException(\Throwable $e)
    {
        return $this->getExceptionHandler()->render($e);
    }

    /**
     * Get an instance of the exception handler.
     */
    protected function getExceptionHandler(): Handler
    {
        $handler = new Handler;
        Application::setHttpMessageFactories($handler);

        return $handler;
    }

    /**
     * Sets HTTP message factories on a given instance.
     */
    public static function setHttpMessageFactories(HttpMessageFactoriesAware $instance)
    {
        $factories = config('app.factories');

        foreach ($factories as $key => $factory_class) {
            $factory = new $factory_class;
            $method = 'set' . strtolower($key);
            $instance->{$method}($factory);
        }
    }
}
