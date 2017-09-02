<?php


namespace RpiLifeDashboard\Unsplash;

use Crew\Unsplash as UnsplashApi;

class Unsplash
{

    const UTM_SOURCE = 'RPI Life Dashboard';

    public function __construct()
    {
        UnsplashApi\HttpClient::init([
            'applicationId'	=> $this->getApplicationId(),
            'utmSource'	=> self::UTM_SOURCE,
        ]);

    }

    private function getApplicationId(): string
    {
        $secrets = require __DIR__.'/../../../secrets/secrets.php';

        if (isset($secrets['unsplash']['applicationId'])) {
            return $secrets['unsplash']['applicationId'];
        } else {
            throw new \Exception("unsplash applicationId missing");
        }
    }

    public function getRandomPhoto()
    {
        return UnsplashApi\Photo::random(
            ['w' => 500, 'h' => 500]
        );
    }

}