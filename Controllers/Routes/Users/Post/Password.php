<?php

namespace Controllers\Routes\Users\Post;

use CommandString\Utils\ArrayUtils;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class Password implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $post = ArrayUtils::trimValues($request->getParsedBody());
        $password = $post['password'] ?? "";
        $confirm_password = $post['confirm_password'] ?? "";
        $errors = [];

        if (empty($password)) {
            $errors[] = "Password cannot be empty";
        }

        if (empty($confirm_password)) {
            $errors[] = "Confirm password cannot be empty";
        }

        if (empty($errors)) {
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }

            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters long";
            }

            if (strlen($password) > 32) {
                $errors[] = "Password must be less than 32 characters long";
            }
        }

        $success = empty($errors);

        if ($success) {
            $args->user
                ->setPassword($password)
                ->commit()
            ;
        }
        
        return new JsonResponse(compact('success', 'errors'), $success ? 200 : 400);
    }
}
