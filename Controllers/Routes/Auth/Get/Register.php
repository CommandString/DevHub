<?php

namespace Controllers\Routes\Auth\Get;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\render;

class Register implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $get = $request->getQueryParams();
        $username = $get['username'] ?? null;
        $email = $get['email'] ?? null;
        $fname = $get['fname'] ?? null;
        $lname = $get['lname'] ?? null;

        return render("auth.register", compact("username", "email", "fname", "lname"));
    }
}
