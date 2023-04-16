<?php

namespace Controllers\Routes\Auth\Get;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\render;

class Login implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $get = $request->getQueryParams();
        $email = $get['email'] ?? null;

        return render("auth.login", compact("email"));
    }
}
