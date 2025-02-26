<?php


namespace Xibo\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\App as App;
use Xibo\Helper\RouteLogProcessor;

/**
 * Log Middleware
 */
class Log implements Middleware
{
    private App $app;

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $container = $this->app->getContainer();

        self::addLogProcessorToLogger($container->get('logger'), $request);

        return $handler->handle($request);
    }

    /**
     * @param LoggerInterface $logger
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public static function addLogProcessorToLogger(
        LoggerInterface $logger,
        Request $request,
    ): void {
        $logger->pushProcessor(new RouteLogProcessor(
            $request->getUri()->getPath(),
            $request->getMethod(),
        ));
    }
}
