<?php


namespace RpiLifeDashboard\WeatherData;


use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

class WeatherData
{
    const LANG = 'en';
    const UNIT = 'metric';

    protected $owm;

    public function __construct()
    {
        $this->owm = new OpenWeatherMap($this->getAPIKey());
    }

    private function getAPIKey(): string
    {
        $secrets = require __DIR__.'/../../../secrets/secrets.php';
        if (isset($secrets['weatherData']['apiKey'])) {
            return $secrets['weatherData']['apiKey'];
        } else {
            throw new \Exception("Weather data api key missing");
        }
    }


    public function getWeatherForecast($query): OpenWeatherMap\WeatherForecast
    {
        try {
            $forecast = $this->owm->getWeatherForecast(
                $query, self::UNIT, self::LANG
            );
        } catch(OWMException $e) {
            echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        } catch(\Exception $e) {
            echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        }

        return $forecast;
    }

}