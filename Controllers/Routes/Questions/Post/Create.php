<?php

namespace Controllers\Routes\Questions\Post;

use Carbon\Carbon;
use CommandString\Utils\ArrayUtils;
use Common\Database\Activity;
use Common\Database\Question;
use HttpSoft\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\render;

class Create implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $post = ArrayUtils::trimValues($request->getParsedBody());
        $required = ["title", "description", "tags"];
        $missing = [];
        $errors = [];

        foreach ($required as $key) {
            if (!isset($post[$key]) || empty($post[$key])) {
                $keyName = match ($key) {
                    "title" => "Title",
                    "description" => "Description",
                    "tags" => "Tags",
                    default => "Unknown",
                };

                $missing[] = $keyName;
            }
        }

        if (!empty($missing)) {
            $errors[] = "Missing required fields: " . implode(", ", $missing);
        }

        $title = $post["title"] ?? "";
        $description = $post["description"] ?? "";
        $tags = $post["tags"] ?? "";

        if (empty($errors)) {
            if (strlen($title) > 100) {
                $errors[] = "Title must be less than 100 characters";
            }

            if (strlen($description) > 3000) {
                $errors[] = "Description must be less than 3000 characters";
            }

            $tagsArray = ArrayUtils::trimValues(explode(",", $tags));

            if (count($tagsArray) > 10) {
                $errors[] = "You can only have 10 tags";
            }

            foreach ($tagsArray as $tag) {
                if (strlen($tag) > 15) {
                    $errors[] = "Tags must be less than 15 characters";
                    break;
                }

                if (preg_match("/[^a-zA-Z0-9]/", $tag)) {
                    $errors[] = "Tags can only contain letters and numbers";
                    break;
                }
            }
        }

        if (empty($errors)) {
            $poster = $args->user;

            $question = new Question;
            $question->setTitle($title);
            $question->setDescription($description);
            $question->setPoster($poster);
            $question->setTags($tagsArray);
            $question->setPosted(new Carbon());
            $question->commit();

            $activity = new Activity;
            $activity->setType(Activity::CREATE_POST);
            $activity->setUser($poster);
            $activity->setData(["question" => $question->getId()]);
            $activity->setDate(new Carbon());
            $activity->commit();

            return new RedirectResponse("/questions/{$question->getId()}");
        }

        return render("questions.create", compact("title", "description", "tags", "errors"));
    }
}
