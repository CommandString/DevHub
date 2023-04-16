<?php

namespace Controllers\Routes\Auth\Post;

use CommandString\Utils\ArrayUtils;
use Common\Database\User;
use HttpSoft\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\env;
use function Common\render;

class Login implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $post = ArrayUtils::trimValues($request->getParsedBody());
        $required = ["email", "password"];
        $errors = [];
        $missingFields = [];

        foreach ($required as $key) {
            if (!isset($post[$key]) || empty($post[$key])) {
                $keyName = match ($key) {
                    "email" => "Email",
                    "password" => "Password",
                    default => "Unknown",
                };

                $missingFields[] = $keyName;
            }
        }

        if (!empty($missingFields)) {
            $errors[] = "Missing required fields: " . implode(", ", $missingFields);
        }

        $email = $post["email"];
        $password = $post["password"];

        if (empty($errors)) {
            $user = User::fetchByEmail($email);

            if ($user === null) {
                $errors[] = "Invalid email or password";
            } else {
                if (!$user->isPasswordCorrect($password)) {
                    $errors[] = "Invalid email or password";
                }
            }
        }

        if (!empty($errors)) {
            return render("auth.login", compact("errors", "email"));
        }

        $controller = env()->sessionController;
        $session = $controller->create();
        $session->set("user_id", $user->getId());
        $res = new RedirectResponse("/");
        $res = $res->withHeader("Set-Cookie", $session->setCookieHeader($session));
        return $res;
    }
}
