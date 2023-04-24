<?php

use Tnapf\Router\Exceptions\HttpNotFound;
use Tnapf\Router\Router;

# Generic Pages
Router::get("/", Controllers\Routes\Render::class)
    ->addStaticArgument("path", "home")
;
Router::get("/privacy", Controllers\Routes\Render::class)
    ->addStaticArgument("path", "legal.privacy")
;
Router::get("/cookies", Controllers\Routes\Render::class)
    ->addStaticArgument("path", "legal.cookies")
;
Router::get("/plans", Controllers\Routes\Plans::class);
Router::post("/newsletter/signup", Controllers\Routes\Newsletter\Signup::class);

# Authentication
Router::get("/register", Controllers\Routes\Auth\Get\Register::class);
Router::post("/register", Controllers\Routes\Auth\Post\Register::class);
Router::get("/login", Controllers\Routes\Auth\Get\Login::class);
Router::post("/login", Controllers\Routes\Auth\Post\Login::class);
Router::get("/logout", Controllers\Routes\Auth\Get\Logout::class);

# User settings
Router::group("/users/{id}", function () {
    Router::get("/", Controllers\Routes\Users\Get\Profile::class);

    Router::group("", function () {
        Router::post("/username", Controllers\Routes\Users\Post\Username::class);
        Router::post("/email", Controllers\Routes\Users\Post\Email::class);
        Router::post("/password", Controllers\Routes\Users\Post\Password::class);
        Router::post("/name", Controllers\Routes\Users\Post\Name::class);
    }, middlewares: [
        Controllers\Middleware\CurrentUser::class
    ]);
}, middlewares: [
    Controllers\Middleware\ValidUserId::class
], parameters: [
    "id" => "[0-9]{16}"
]);

# API
Router::get("/api/users/{id}", Controllers\Routes\Users\Get\Json::class)
    ->setParameter("id", "[0-9]{16}")
    ->addMiddleware(Controllers\Middleware\ValidUserId::class)
;

# Questions
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

Router::get("/questions/{id}", Controllers\Routes\Questions\Get\Question::class)
    ->addMiddleware(Controllers\Middleware\ValidQuestionId::class)
;

Router::group("/questions/{id}", function () {
    Router::post("/comments", Controllers\Routes\Comments\Post\Create::class);
    Router::post("/upvote", Controllers\Routes\Questions\Post\Upvote::class);
    Router::post("/downvote", Controllers\Routes\Questions\Post\Downvote::class);
}, middlewares: [
    Controllers\Middleware\ValidQuestionId::class,
    Controllers\Middleware\LoggedIn::class
], parameters: [
    "id" => "[0-9]{16}"
]);

# Catchers
Router::catch(HttpNotFound::class, Controllers\Catchers\NotFound::class);
