<?php

namespace Controllers\Routes\Users;

use Carbon\Carbon;
use Common\Database\Question;
use Common\Database\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\render;

class Profile implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        /** @var User */
        $user = $args->user;

        $questions = $user->fetchQuestions();

        return render("users.profile", compact("user", "questions"));
    }
}
