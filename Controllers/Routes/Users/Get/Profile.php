<?php

namespace Controllers\Routes\Users\Get;

use Common\Database\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;
use Twig\Functions\GetCurrentUser;

use function Common\createOgTags;
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
        $user->fetchActivities();

        $questions = $user->fetchQuestions();
        $og = createOgTags("{$user->getUsername()}'s profile", "/users/{$user->getId()}", "");
        $isCurrentUser = GetCurrentUser::method()?->getId() === $user->getId();

        return render("users.profile", compact("user", "questions", "og", "isCurrentUser"));
    }
}
