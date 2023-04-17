<?php

namespace Controllers\Middleware;

use HttpSoft\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;
use Twig\Functions\IsLoggedIn;

class LoggedIn implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        if (!IsLoggedIn::method()) {
            return new RedirectResponse("/login");
        }

        return $next($request, $response, $args);
    }
}
