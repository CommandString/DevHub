<?php

use Tnapf\Router\Exceptions\HttpNotFound;
use Tnapf\Router\Router;

Router::get("/", Controllers\Routes\Home::class);

Router::get("/privacy", Controllers\Routes\Legal\Privacy::class);
Router::get("/cookies", Controllers\Routes\Legal\Cookies::class);

Router::get("/register", Controllers\Routes\Auth\Get\Register::class);
Router::post("/register", Controllers\Routes\Auth\Post\Register::class);
Router::get("/login", Controllers\Routes\Auth\Get\Login::class);
Router::post("/login", Controllers\Routes\Auth\Post\Login::class);
Router::get("/logout", Controllers\Routes\Auth\Get\Logout::class);

Router::get("/plans", Controllers\Routes\Plans::class);

Router::post("/newsletter/signup", Controllers\Routes\Newsletter\Signup::class);

Router::group("/users/{id}", function () {
    Router::get("/", Controllers\Routes\Users\Profile::class)
        ->setParameter("id", "[0-9]{16}")
        ->addMiddleware(Controllers\Middleware\ValidUserId::class)
    ;
});

Router::get("/questions/{id}", Controllers\Routes\Questions\Get\Question::class)
    ->setParameter("id", "[0-9]{16}")
    ->addMiddleware(Controllers\Middleware\ValidQuestionId::class)
;
Router::get("/questions", Controllers\Routes\Questions\Get\Questions::class);
Router::get("/questions/search/{query}", Controllers\Routes\Questions\Get\Query::class)
    ->setParameter("query", "[a-zA-Z0-9]*")
;
Router::get("/questions/create", Controllers\Routes\Questions\Get\Create::class)
    ->addMiddleware(Controllers\Middleware\LoggedIn::class)
;
Router::post("/questions/create", Controllers\Routes\Questions\Post\Create::class)
    ->addMiddleware(Controllers\Middleware\LoggedIn::class)
;
Router::get("/questions/{id}/upvote", Controllers\Routes\Questions\Get\Upvote::class)
    ->setParameter("id", "[0-9]{16}")
    ->addMiddleware(Controllers\Middleware\LoggedIn::class)
    ->addMiddleware(Controllers\Middleware\ValidQuestionId::class)
;
Router::get("/questions/{id}/downvote", Controllers\Routes\Questions\Get\Downvote::class)
    ->setParameter("id", "[0-9]{16}")
    ->addMiddleware(Controllers\Middleware\LoggedIn::class)
    ->addMiddleware(Controllers\Middleware\ValidQuestionId::class)
;

Router::catch(HttpNotFound::class, Controllers\Catchers\NotFound::class);
