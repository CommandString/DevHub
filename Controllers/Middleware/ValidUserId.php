<?php

namespace Controllers\Middleware;

use Common\Database\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Exceptions\HttpNotFound;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class ValidUserId implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $id = $request->getQueryParams()['id'] ?? $args->id ?? null;
        $user = null;

        if ($id !== null) {
            $user = User::fetchById($id);
            $args->user = $user;
        }

        if ($user === null) {
            throw new HttpNotFound($request);
        }

        return $next($request, $response, $args);
    }
}
