<?php

namespace Controllers\Middleware;

use HttpSoft\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;
use Twig\Functions\GetCurrentUser;

class LoggedIn implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $user = GetCurrentUser::method();

        if ($user === null) {
            return new RedirectResponse("/login");
        }

        $args->user = $user;

        return $next($request, $response, $args);
    }
}
