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
    $forecast = $weatherData->getWeatherForecast('Reading');

    /*$unsplash = (new \RpiLifeDashboard\Unsplash\Unsplash())
        ->getRandomPhoto();*/

    $trains = (new \RpiLifeDashboard\TrainTimes\TrainTimes())
        ->getTransformedDepBoardWithDetails("RDG","ACT");



    //TODO get random quote

    return $app['twig']->render('dashboard.html.twig', array(
        'name' => $name,
        'forecast' => $forecast,
        'unsplash' => [
            'url' => $unsplash->urls['custom'] ?? '',
        ],
        'trains' => $trains
    ));
});

$app->get('/debug', function () use ($app) {

    $events = (new \RpiLifeDashboard\Calendar\GoogleCalendar())->listEvents();


    print "Upcoming events:\n";
    foreach ($events as $event) {
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        printf("%s (%s)\n", $event->getSummary(), $start);
    }
    die;

    //$array = json_decode(json_encode($trains), true);

    echo '<pre>';
    print_r($trains);
    die;

});

$app->run();