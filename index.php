<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

// Enable debuggin mode for developer friendly error messages
$app['debug'] = true;

// Default route
$app->get('/', function () use ($app) {
    return 'Hacker News';
});

$app->run();