<?php

namespace Controllers\Routes\Questions\Get;

use Carbon\Carbon;
use Common\Database\Question;
use Common\Database\User;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\env;
use function Common\renderHtml;

class Query implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $get = $request->getQueryParams();
        $query = $args->query;

        $stmt = env()->driver->prepare("SELECT * FROM questions WHERE title LIKE :query");
        $stmt->execute(['query' => "%$query%"]);
        $rows = $stmt->fetchAll();
        $questions = [];

        foreach ($rows as $row) {
            $questions[] = Question::createFromDatabase(
                $row['id'],
                $row['title'],
                $row['description'],
                (new Carbon())->setTimestamp($row['posted']),
                User::fetchById($row['poster']),
                json_decode($row['tags'], true),
                $row['upvotes'],
                $row['downvotes'],
                $row['answered'],
                $row['views']
            );
        }

        if (isset($get['html'])) {
            foreach ($questions as &$question) {
                $question = renderHtml("questions.questionItem", compact("question"));
            }
        }

        return new JsonResponse($questions);
    }
}
