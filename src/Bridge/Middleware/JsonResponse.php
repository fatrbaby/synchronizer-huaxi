<?php

namespace Bridge\Middleware;

use Slim\Http\Body;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class JsonResponse
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response = $next($request, $response);

        $body = $response->getBody();

        if (empty(json_encode($body))) {
            return $response;
        }

        $response = $response->withBody(
            new Body(fopen('php://temp', 'r+'))
        );

        $response->write($body);
        $response = $response->withAddedHeader('Content-Type','application/json');

        return $response;
    }
}
