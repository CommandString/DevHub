<?php

namespace Controllers\Routes\Auth\Get;

use HttpSoft\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\env;

class Logout implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $res = new RedirectResponse("/");

        if (isset($_COOKIE['session_id'])) {
            env()->sessionController->delete($_COOKIE['session_id']);
            $res = $res->withAddedHeader("Set-Cookie", "session_id=; Path=/; Expires=Thu, 01 Jan 1970 00:00:00 GMT");
        }

        return $res;
    }
}
