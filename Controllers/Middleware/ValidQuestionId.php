<?php

namespace Controllers\Middleware;

use Common\Database\Question;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Exceptions\HttpNotFound;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

class ValidQuestionId implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $id = $request->getQueryParams()['id'] ?? $args->id ?? null;
        $question = null;

        if ($id !== null) {
            $question = Question::fetchById((int)$id);
            $args->question = $question;
        }

        if ($question === null) {
            throw new HttpNotFound($request);
        }

        return $next($request, $response, $args);
    }
}
