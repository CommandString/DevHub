<?php

use Tnapf\Router\Exceptions\HttpNotFound;
use Tnapf\Router\Router;

Router::get("/", Controllers\Routes\Home::class);
Router::get("/privacy", Controllers\Routes\Legal\Privacy::class);
Router::get("/cookies", Controllers\Routes\Legal\Cookies::class);
Router::catch(HttpNotFound::class, Controllers\Catchers\NotFound::class);
Router::get("/register", Controllers\Routes\Auth\Get\Register::class);
Router::post("/register", Controllers\Routes\Auth\Post\Register::class);
Router::get("/login", Controllers\Routes\Auth\Get\Login::class);
Router::post("/login", Controllers\Routes\Auth\Post\Login::class);
Router::get("/logout", Controllers\Routes\Auth\Get\Logout::class);
Router::get("/plans", Controllers\Routes\Plans::class);
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
Router::post("/newsletter/signup", Controllers\Routes\Newsletter\Signup::class);
