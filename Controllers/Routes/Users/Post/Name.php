<?php

namespace Controllers\Routes\Users\Post;

use CommandString\Utils\ArrayUtils;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class Name implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $post = ArrayUtils::trimValues($request->getParsedBody());
        $required = ['fname', 'lname'];
        $missing = [];
        $user = $args->user;

        foreach ($required as $key) {
            if (!isset($post[$key]) || empty($post[$key])) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            $errors[] = "Please fill in the following fields: " . implode(', ', $missing);
        }

        $checkName = function(string $toCheck, string $nameType) use (&$errors, $user): void
        {
            if (!preg_match('/^[a-zA-Z]+$/', $toCheck)) {
                $errors[] = "$nameType can only contain letters";
            }

            if (strlen($toCheck) > 30) {
                $errors[] = "$nameType must be less than 30 characters";
            }

            if (strlen($toCheck) < 2) {
                $errors[] = "$nameType must be more than 2 characters";
            }
        };

        if (empty($errors)) {
            $fname = ucfirst(strtolower($post['fname']));
            $lname = ucfirst(strtolower($post['lname']));

            $checkName($fname, 'First name');
            $checkName($lname, 'Last name');

            if (strtolower($user->getFullName()) === strtolower("{$fname} {$lname}")) {
                $errors[] = "Your name hasn't changed";
            }
        }

        if (empty($errors)) {
            $user
                ->setFname($fname)
                ->setLname($lname)
                ->commit()
            ;
        }

        $success = empty($errors);

        return new JsonResponse(compact('success', 'errors'), $success ? 200 : 400);
    }
}
