<?php

namespace Controllers\Routes\Newsletter;

use Common\Database\Newsletter;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class Signup implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $post = $request->getParsedBody();
        $email = $post["email"] ?? null;
        $errors = [];

        if (!isset($email)) {
            $errors[] = "Email is required";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is invalid";
        }

        if (Newsletter::fetchByEmail($email) !== null) {
            $errors[] = "Email is already signed up";
        }

        if (empty($errors)) {
            $newsletter = new Newsletter($email);
            $newsletter->commit();
        }

        $success = empty($errors);

        return new JsonResponse(compact("errors", "success"));
    }
}
