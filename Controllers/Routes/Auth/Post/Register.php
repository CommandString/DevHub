<?php

namespace Controllers\Routes\Auth\Post;

use Carbon\Carbon;
use CommandString\Utils\ArrayUtils;
use Common\Database\User;
use HttpSoft\Response\RedirectResponse;
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
        $post = ArrayUtils::trimValues($request->getParsedBody());
        $required = ["username", "password", "confirm_password", "email", "fname", "lname"];
        $errors = [];
        $missingFields = [];

        foreach ($required as $key) {
            if (!isset($post[$key]) || empty($post[$key])) {
                $keyName = match ($key) {
                    "username" => "Username",
                    "password" => "Password",
                    "email" => "Email",
                    "fname" => "First Name",
                    "lname" => "Last Name",
                    "confirm_password" => "Confirm Password",
                    default => "Unknown",
                };

                $missingFields[] = $keyName;
            }
        }

        if (!empty($missingFields)) {
            $errors[] = "Missing required fields: " . implode(", ", $missingFields);
        }

        $username = $post["username"];
        $password = $post["password"];
        $email = $post["email"];
        $fname = $post["fname"];
        $lname = $post["lname"];
        $confirm_password = $post["confirm_password"];

        if (empty($errors)) {
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }

            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }

            if (strlen($username) < 3) {
                $errors[] = "Username must be at least 3 characters";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email address";
            }

            if (User::fetchByUsername($username) !== null) {
                $errors[] = "Username already taken";
            }

            if (User::fetchByEmail($email) !== null) {
                $errors[] = "Email already taken";
            }

            if (preg_match("/[^a-zA-Z0-9_]/", $username)) {
                $errors[] = "Username can only contain letters, numbers, and underscores";
            }

            if (strlen($fname) > 30) {
                $errors[] = "First name must be less than 50 characters";
            }

            if (strlen($lname) > 30) {
                $errors[] = "Last name must be less than 50 characters";
            }

            if (strlen($email) > 100) {
                $errors[] = "Email must be less than 100 characters";
            }

            if (strlen($username) > 50) {
                $errors[] = "Username must be less than 50 characters";
            }
        }

        if (empty($errors)) {
            $user = new User();
            $user
                ->setUsername($username)
                ->setPassword($password)
                ->setEmail($email)
                ->setFname($fname)
                ->setLname($lname)
                ->setRegistered(new Carbon())
                ->commit()
            ;

            return new RedirectResponse("/login?email=" . urlencode($email));
        }

        return render("auth.register", compact("username", "email", "fname", "lname", "password", "errors"));
    }
}
