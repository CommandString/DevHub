<?php

namespace Controllers\Routes\Users\Post;

use CommandString\Utils\ArrayUtils;
use Common\Database\User;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class Email implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $post = ArrayUtils::trimValues($request->getParsedBody());
        $email = $post["email"] ?? null;
        $errors = [];

        if ($email === null) {
            $errors[] = "Missing required field: Email";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email";
        }

        if (strlen($email) > 100) {
            $errors[] = "Email must be less than 100 characters";
        }

        if (User::fetchByEmail($email) !== null) {
            $errors[] = "Email already in use";
        }

        $success = empty($errors);

        if ($success) {
            $args->user
                ->setEmail($email)
                ->commit()
            ;
        }

        return new JsonResponse(compact("success", "errors"), $success ? 200 : 400);
    }
}
