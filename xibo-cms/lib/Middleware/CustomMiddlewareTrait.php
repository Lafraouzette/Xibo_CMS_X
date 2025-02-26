<?php

namespace Xibo\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

/**
 * Trait CustomMiddlewareTrait
 * Add this trait to all custom middleware
 * @package Xibo\Middleware
 */
trait CustomMiddlewareTrait
{
    /** @var \Slim\App */
    private $app;

    /**
     * @param \Slim\App $app
     * @return $this
     */
    public function setApp(App $app)
    {
        $this->app = $app;
        return $this;
    }

    /**
     * @return \Slim\App
     */
    protected function getApp()
    {
        return $this->app;
    }

    /**
     * @return \DI\Container|\Psr\Container\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->app->getContainer();
    }

    /**
     * @param $key
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getFromContainer($key): mixed
    {
        return $this->getContainer()->get($key);
    }

    /**
     * Append public routes
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array $routes
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function appendPublicRoutes(ServerRequestInterface $request, array $routes): ServerRequestInterface
    {
        // Set some public routes
        return $request->withAttribute(
            'publicRoutes',
            array_merge($request->getAttribute('publicRoutes', []), $routes)
        );
    }
}
