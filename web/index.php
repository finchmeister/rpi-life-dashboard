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
    $weather = $weatherData->getCurrentWeather('Reading');

    /*$unsplash = (new \RpiLifeDashboard\Unsplash\Unsplash())
        ->getRandomPhoto();*/

    $trains = (new \RpiLifeDashboard\TrainTimes\TrainTimes())
        ->getTransformedDepBoardWithDetails("RDG","ACT");


    $quote = (new \RpiLifeDashboard\Quotes\RandomQuote())->getQuote();



    return $app['twig']->render('dashboard.html.twig', array(
        'time' => new DateTime("now"),
        'weather' => $weather,
        'unsplash' => [
            'url' => $unsplash->urls['custom'] ?? 'https://images.unsplash.com/photo-1503743804510-6ed2d7322647?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=500&h=500&fit=crop&s=e49a9602d82c99224cfdd59b63593ba5',
        ],
        'trains' => $trains,
        'quote' => $quote,
    ));
});

$app->get('/debug', function () use ($app) {

    $weatherData = new \RpiLifeDashboard\WeatherData\WeatherData();
    $object = $weatherData->getCurrentWeather('London');

    var_dump($object->temperature->now);die;
    $array = json_decode(json_encode($object->temperature->getDescription()), true);

    /*$events = (new \RpiLifeDashboard\Calendar\GoogleCalendar())->listEvents();

    die;

    print "Upcoming events:\n";
    foreach ($events as $event) {
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        printf("%s (%s)\n", $event->getSummary(), $start);
    }
    die;*/

    //$array = json_decode(json_encode($trains), true);

    echo '<pre>';
    print_r($array);

    die;

});

$app->run();