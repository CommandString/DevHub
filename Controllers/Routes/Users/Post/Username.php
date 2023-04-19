<?php

namespace Controllers\Routes\Users\Post;

use CommandString\Utils\ArrayUtils;
use Common\Database\User;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;
use Twig\Functions\GetCurrentUser;

class Username implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $post = ArrayUtils::trimValues($request->getParsedBody());
        $username = $post['username'] ?? "";
        $errors = [];

        if (empty($username)) {
            $errors[] = "Username cannot be empty";
        }

        if (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters long";
        }

        if (strlen($username) > 50) {
            $errors[] = "Username must be less than characters long";
        }

        if (preg_match('/[^a-zA-Z0-9_]/', $username)) {
            $errors[] = "Username can only contain letters, numbers and underscores";
        }

        $user = $args->user;

        if (User::fetchByUsername($username) !== null) {
            $errors[] = "Username is already taken";
        } else if ($user->getUsername() === $username) {
            $errors[] = "Username has not changed";
        }

        if (empty($errors)) {
            $user->setUsername($username);
            $user->commit();
        }

        $success = empty($errors);

        return new JsonResponse(compact('success', 'errors'), $success ? 200 : 400);
    }
}
