<?php

namespace Controllers\Routes\Questions\Get;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;
use Common\Database\Question as QuestionModel;
use Common\Database\User;

use function Common\driver;
use function Common\render;

class Questions implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $query = $request->getQueryParams()['q'] ?? null;
        $questions = driver()->query("SELECT * FROM questions")->fetchAll();

        foreach ($questions as &$question) {
            $question = QuestionModel::createFromDatabase(
                $question['id'],
                $question['title'],
                $question['description'],
                (new Carbon())->setTimestamp($question['posted']),
                User::fetchById($question['poster']),
                json_decode($question['tags']),
                $question['upvotes'],
                $question['downvotes'],
                $question['answered'],
                $question['views']
            );
        }

        return render("questions.questions", compact("questions", "query"));
    }
}
