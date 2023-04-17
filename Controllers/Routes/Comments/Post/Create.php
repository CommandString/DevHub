<?php

namespace Controllers\Routes\Comments\Post;

use Carbon\Carbon;
use CommandString\Utils\ArrayUtils;
use Common\Database\Activity;
use Common\Database\Comment;
use Common\Database\Question;
use Common\Database\User;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class Create implements RequestHandlerInterface
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

        $post = ArrayUtils::trimValues($request->getParsedBody());
        $description = $post["comment"] ?? "";
        $errors = [];

        if (empty($description)) {
            $errors[] = "Comment cannot be empty";
        }

        if (strlen($description) > 2000) {
            $errors[] = "Comment cannot be over 2000 characters";
        }

        $success = empty($errors);

        if ($success) {
            $comment = (new Comment)
                ->setQuestion($question)
                ->setDescription($description)
                ->setPoster($user)
                ->setPosted(new Carbon)
                ->commit()
            ;

            (new Activity)
                ->setType(Activity::CREATE_COMMENT)
                ->setData([
                    "question" => $question->getId(),
                ])
                ->setUser($user)
                ->setDate(new Carbon)
                ->commit()
            ;
        }

        return new JsonResponse(compact("errors", "success"));
    }
}
