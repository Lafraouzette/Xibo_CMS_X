<?php


namespace Xibo\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Xibo\Helper\Random;

/**
 * CSP middleware to output CSP headers and add a CSP nonce to the view layer.
 */
class Csp implements Middleware
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * Call middleware
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Generate a nonce
        $nonce = Random::generateString(8);

        // Create CSP header
        $csp = 'object-src \'none\'; script-src \'nonce-' . $nonce . '\'';
        $csp .= ' \'unsafe-inline\' \'unsafe-eval\' \'strict-dynamic\' https: http:;';
        $csp .= ' base-uri \'self\';';
        $csp .= ' frame-ancestors \'self\';';

        // Store it for use in the stack if needed
        $request = $request->withAttribute('cspNonce', $nonce);

        // Assign it to our view
        $this->container->get('view')->offsetSet('cspNonce', $nonce);

        // Call next middleware.
        $response = $handler->handle($request);

        // Add our header
        return $response
            ->withAddedHeader('X-Frame-Options', 'SAMEORIGIN')
            ->withAddedHeader('Content-Security-Policy', $csp);
    }
}
