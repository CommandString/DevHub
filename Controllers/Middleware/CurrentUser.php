<?php

namespace Controllers\Middleware;

use Controllers\Middleware\LoggedIn;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class CurrentUser implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        LoggedIn::handle($request, $response, $args, function ($request, $response, $a) use (&$args) {
            $args->currentUser = $a->user;
            return $response;
        });

        if ($args->user !== $args->currentUser) {
            return $response->withStatus(403);
        }

        return $next($request, $response, $args);
    }
}
