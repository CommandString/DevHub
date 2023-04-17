<?php

namespace Controllers\Routes\Questions\Get;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;
use Twig\Functions\Ellipses;
use Common\Database\Question as QuestionModel;

use function Common\createOgTags;
use function Common\render;

class Question implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        /** @var QuestionModel */
        $question = $args->question;
        $question->view()->commit();

        $og = createOgTags($question->getTitle(), "/questions/{$question->getId()}", Ellipses::method($question->getDescription(), 100));

        return render("questions.question", compact("question", "og"));
    }
}
