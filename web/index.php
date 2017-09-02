<?php

// for dev mode with php server
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->get('/hello/{name}', function ($name) use ($app) {

    // Do weather stuff
    $weatherData = new \RpiLifeDashboard\WeatherData\WeatherData();
    $forecast = $weatherData->getWeatherForecast();

    $unsplash = (new \RpiLifeDashboard\Unsplash\Unsplash())
        ->getRandomPhoto();


    return $app['twig']->render('dashboard.html.twig', array(
        'name' => $name,
        'forecast' => $forecast,
        'unsplash' => [
            'url' => $unsplash->urls['custom'],
        ],
    ));
});

$app->run();