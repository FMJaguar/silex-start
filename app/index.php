<?php
require_once __DIR__."/../vendor/autoload.php";

$app = new Silex\Application();

$app->register(new Silex\Provider\SessionServiceProvider());
$app->before(function ($request) {
    $request->getSession()->start();
});

$r = new App\Logging\IdRequestProcessor();

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/development.log'
));

$app['monolog'] = $app->share($app->extend('monolog', function($monolog, $app) use ($r) {
    $monolog->pushProcessor($r);
    return $monolog;
}));

//ENV
if (isset($_SERVER['ENVIRONMENT'])) {
    $app['environment'] = $_SERVER['ENVIRONMENT'];
    $app['debug'] = true;
} else {
    $app['environment'] = 'prod';
    $app['debug'] = false;
}


$app->get('/', function() use ($app) {
	$app['monolog']->addDebug("Test!");
	return "hi";
});

// web/index.php

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

//$app = require __DIR__.'/../src/app.php';
$app->run();