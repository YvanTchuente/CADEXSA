<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\MiddleWare\TextStream;
use Application\MiddleWare\Router\Router;
use Application\MiddleWare\ServerRequest;
use Application\MiddleWare\Router\RouteNotFoundException;

class RouterTest extends TestCase
{
    /** @var Router */
    private $router;

    public function setUp(): void
    {
        $request = new ServerRequest("get", "http://localhost/cms/pictures/upload", body: new TextStream("Tests"));
        $this->router = new Router($request);
    }

    public function testGet()
    {
        // Given that a GET route is registered
        $action = fn () => 'Get upload page';
        $this->router->get('/cms/pictures/upload', $action);

        // Assert that GET routes includes the new route
        $routes = $this->router->getRoutes()['GET'];
        $this->assertArrayHasKey('/cms/pictures/upload', $routes);
    }

    public function testPost()
    {
        // Given that a POST route is registered
        $action = fn () => 'Post to upload page';
        $this->router->post('/cms/pictures/upload', $action);

        // Assert that POST routes includes the new route
        $routes = $this->router->getRoutes()['POST'];
        $this->assertArrayHasKey('/cms/pictures/upload', $routes);
    }

    public function testSetDefaultAction()
    {
        $default_action = fn () => "Default action";
        $this->router->setDefaultAction($default_action);
        // Assert the router resolves the default route when no route is registered
        $this->assertSame("Default action", $this->router->resolve());
    }

    public function testGetRoutes()
    {
        // After router initialization, assert that there is no routes
        $this->assertEmpty($this->router->getRoutes());

        // Given that a route is registered
        $this->router->get('/cms/pictures/upload', fn () => ['Get upload page']);

        // Assert that a route was registered
        $this->assertNotEmpty($this->router->getRoutes());
    }

    public function test_router_not_resolve_when_no_routes_is_registered()
    {
        // When no routes were registered, assert that RouterException is thrown
        $this->expectExceptionMessage("No route was registered");
        $this->router->resolve();
    }

    public function test_router_throws_execption_when_not_found_route()
    {
        // Given several routes are registered
        $this->router->post('/cms/pictures/add', fn () => 'Add a picture using the upload page');
        $this->router->get('/cms/pictures/delete', fn () => 'Delete a picture using the upload page');
        $this->router->post('/cms/pictures/update', fn () => 'Replace a picture using the upload page');
        // Expect a except as exception as the correct route is not found
        $this->expectException(RouteNotFoundException::class);
        $this->router->resolve();
    }

    public function testMatch()
    {
        // Given that several routes were registered
        $this->router->get('/cms/pictures/upload', fn () => 'Get upload page');

        $match = $this->router->match();
        $this->assertSame('Get upload page', call_user_func($match));
    }

    public function test_router_resolve_a_route()
    {
        // Given that several routes were registered
        $this->router->get('/cms/pictures/upload', fn () => 'Get upload page');
        $this->router->post('/cms/pictures/upload', fn () => 'Post to upload page');

        // Assert the router resolves the correct route
        $this->assertSame('Get upload page', $this->router->resolve());
    }
}
