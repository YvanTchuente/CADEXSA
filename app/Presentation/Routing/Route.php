<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Routing;

use Cadexsa\Infrastructure\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Presentation\Routing\Matching\Validator;
use Cadexsa\Presentation\Http\Controllers\Controller;
use Cadexsa\Presentation\Routing\Matching\UriValidator;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAware;
use Cadexsa\Presentation\Routing\Matching\MethodValidator;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAwareTrait;

/**
 * Represents a route.
 */
class Route implements HttpMessageFactoriesAware
{
    use HttpMessageFactoriesAwareTrait;

    /**
     * The route name.
     */
    private string|null $name = null;

    /**
     * The route path.
     * 
     * A URL path pattern of the route.
     */
    private string $path;

    /**
     * The HTTP methods the route responds to.
     * 
     * @var string[]
     */
    private array $methods;

    /**
     * The route handler.
     */
    public string $handler;

    /**
     * The regular expression requirements.
     */
    private array $requirements = [];

    /**
     * The parameter names for the route.
     */
    private array|null $parameterNames;

    /**
     * The array of matched parameters.
     */
    private array|null $parameters;

    /**
     * Route attributes.
     */
    private array $attributes = [];

    /**
     * The validators used by the routes.
     * 
     * @var Validator[]
     */
    public static array $validators;

    /**
     * Defines a route.
     * 
     * The route may have parameters; these parameters are defined with enclosing brackets
     * in `url` such as **{articleId}** in **'/article/{articleId}'** where **articleId** is a route
     * parameter. 
     * 
     * Route parameters may have constraints; these constraints are defined with regular expressions
     * in `constraints`, indexed by the corresponding parameter name.
     * 
     * For example in the array, `['articleId' => '\d+']`, `\d+` is the regular expression constraint
     * and `articleId` is the corresponding parameter.
     *
     * @param string|string[] $methods The HTTP methods
     * @param string $path The URL path pattern
     * @param string $handler A controller class name
     */
    public function __construct(string|array $methods, string $path, string $handler)
    {
        settype($methods, "array");
        if (!$path) {
            throw new \DomainException('The uri pattern is required.');
        } elseif (!is_subclass_of($handler, Controller::class)) {
            throw new \LogicException("$handler is not a valid controller.");
        }

        array_walk($methods, function ($method, $key) {
            if (!in_array($method, Router::$methods)) {
                throw new \DomainException("$method is not a valid HTTP method");
            }
        });

        $this->path = $path;
        $this->methods = $methods;
        $this->handler = $handler;

        if (in_array('GET', $this->methods) && !in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }
    }

    /**
     * Get the route name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the route name.
     */
    public function name(string $name)
    {
        if (empty($name)) {
            throw new \LengthException('The name is empty.');
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Get the URL path pattern of the route.
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Get the HTTP methods the route responds to.
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * Set the route handler.
     * 
     * @param string $handler A controller class name
     */
    public function uses(string $handler)
    {
        if (!is_subclass_of($handler, Controller::class)) {
            throw new \DomainException("[$handler] is not a controller.");
        }
        $this->handler = $handler;
    }

    /**
     * Set a regular expression requirement on the route.
     */
    public function where(string $parameter, string $expression = null)
    {
        $this->requirements[$parameter] = $expression;
        return $this;
    }

    /**
     * Set a list of regular expression requirements on the route.
     */
    public function setRequirements(array $requirements)
    {
        foreach ($requirements as $parameter => $expression) {
            $this->where($parameter, $expression);
        }
        return $this;
    }

    /**
     * Get the key / value list of parameters for the route.
     *
     * @throws \LogicException
     */
    public function parameters()
    {
        if (isset($this->parameters)) {
            return $this->parameters;
        }

        throw new \LogicException('The route is not bound.');
    }

    /**
     * Get a given parameter from the route.
     *
     * @return string|object|null
     */
    public function parameter(string $name, string|object $default = null)
    {
        return $this->parameters()[$name] ?? $default;
    }

    /**
     * Get the key / value list of parameters without null values.
     *
     * @return array
     */
    public function parametersWithoutNulls()
    {
        return array_filter($this->parameters(), function ($p) {
            return !is_null($p);
        });
    }

    /**
     * Get all of the parameter names for the route.
     */
    public function parameterNames()
    {
        if (isset($this->parameterNames)) {
            return $this->parameterNames;
        }

        return $this->parameterNames = array_map(function ($name) {
            return trim($name, '?');
        }, $this->compileParameterNames());
    }

    /**
     * Get the parameter names for the route.
     */
    private function compileParameterNames()
    {
        preg_match_all('/\{(.*?)\}/', $this->path, $matches);

        return $matches[1];
    }

    /**
     * Run the route controller and return the response.
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->bind($request);
        $queryParams = array_merge($request->getQueryParams(), $this->parameters());
        $request = $request->withQueryParams($queryParams);

        return $this->makeController($this->handler)->handle($request);
    }

    /**
     * Determine if the route matches a given request.
     * 
     * @return bool
     */
    public function matches(ServerRequestInterface $request)
    {
        foreach (self::getValidators() as $validator) {
            if (!$validator->matches($this, $request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the middlewares attached to the route.
     * 
     * @return string[]
     */
    public function getMiddleware()
    {
        return $this->attributes['middleware'] ?? [];
    }

    /**
     * Get or set the middlewares attached to the route.
     */
    public function setMiddleware(array|string $middleware)
    {
        if (!is_array($middleware)) {
            $middleware = func_get_args();
        }

        foreach ($middleware as $index => $value) {
            $middleware[$index] = (string) $value;
        }

        $this->attributes['middleware'] = array_merge($this->attributes['middleware'], $middleware);

        return $this;
    }

    /**
     * Compile the uri path pattern to a regular expression.
     *
     * @return string
     */
    public function compilePath()
    {
        $path = $this->path;

        if ($this->requirements) {
            foreach ($this->requirements as $parameterName => $requirement) {
                if (in_array($parameterName . '?', $this->compileParameterNames())) {
                    $replacements[sprintf("{%s?}", $parameterName)] = $requirement;
                } else {
                    $replacements[sprintf("{%s}", $parameterName)] = $requirement;
                }
            }
        }

        if (isset($replacements)) {
            foreach ($replacements as $key => $value) {
                $pattern = sprintf("/%s/", preg_quote($key, '?'));

                if (str_ends_with($key, '?}')) {
                    $path = preg_replace($pattern, "?($value)?", $path);
                } else {
                    $path = preg_replace($pattern, "($value)", $path);
                }
            }
        } else {
            if ($this->compileParameterNames()) {
                foreach ($this->compileParameterNames() as $parameterName) {
                    $pattern = sprintf("/{%s}/", $parameterName);

                    if (str_ends_with($parameterName, '?')) {
                        $path = preg_replace($pattern, "?(.*?)?", $path);
                    } else {
                        $path = preg_replace($pattern, "(.*?)", $path);
                    }
                }
            }
        }

        $path = sprintf("#^%s$#", $path);

        return $path;
    }

    /**
     * Get route attributes.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set route attributes.
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get the route validators for the instance.
     *
     * @return array
     */
    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }
        return static::$validators = [
            new UriValidator, new MethodValidator
        ];
    }

    /**
     * Dynamically access route parameters.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->parameter($key);
    }

    /**
     * Bind the route to a given request for execution.
     */
    private function bind(ServerRequestInterface $request)
    {
        $this->parameters = (new RouteParameterBinder($this))
            ->parameters($request);

        return $this;
    }

    private function makeController(string $handler): Controller
    {
        $handler = new $handler;
        Application::setHttpMessageFactories($handler);

        return $handler;
    }
}
