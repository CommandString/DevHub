<?php

namespace Controllers\Routes\Questions\Post;

use Carbon\Carbon;
use Common\Database\Activity;
use Common\Database\Question;
use Common\Database\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class Upvote implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        /** @var Question */
        $question = $args->question;

        /** @var User */
        $user = $args->user;

        if ($question->hasAlreadyVoted($user)) {
            return $response->withStatus(400);
        }

        (new Activity)
            ->setUser($user)
            ->setType(Activity::UPVOTE_QUESTION)
            ->setData([
                "question" => $question->getId()
            ])
            ->setDate(new Carbon)
            ->commit()
        ;

        $question->upvote()->commit();

        return $response->withStatus(200);
    }
}