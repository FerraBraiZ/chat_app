<?php

use Phalcon\Mvc\Micro;

$app = new Micro();

$app->get("/", function () {
    require 'views/home.html';
});

$app->get("/atendente", function () {
    require 'views/atendente.html';
});

$app->get("/suport", function () {
    require 'views/suport.html';
});

$app->get("/chat", function () {
    require 'views/chat.html';
});

$app->handle();
