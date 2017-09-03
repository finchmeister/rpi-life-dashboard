<?php


namespace RpiLifeDashboard\TrainTimes;


use RpiLifeDashboard\TrainTimes\vendor\railalefan\OpenLDBWS;

class TrainTimes
{

    protected $openLDBWS;

    public function __construct()
    {
        $this->openLDBWS = new OpenLDBWS(
            $this->getAccessToken()
        );
    }

    private function getAccessToken(): string
    {
        $secrets = require __DIR__.'/../../../secrets/secrets.php';
        if (isset($secrets['train']['accessToken'])) {
            return $secrets['train']['accessToken'];
        } else {
            throw new \Exception("Train AccessToken missing");
        }
    }

    public function getTransformedDepBoardWithDetails(
        $crs,
        $filterList,
        $timeOffset="",
        $timeWindow=""
    ) {
        /** @var \stdClass $response */
        $response = $this->openLDBWS->GetDepBoardWithDetails(
            10,
            $crs,
            $filterList,
            $timeOffset,
            $timeWindow
        );
        return $this->transformGetDepBoardWithDetailsResponse($response);
    }

    private function transformGetDepBoardWithDetailsResponse(\stdClass $response): array
    {
        $filterCrs = $response->GetStationBoardResult->filtercrs;
        $trains = [];

        foreach ($response->GetStationBoardResult->trainServices->service as $train) {
            $std = $train->std;
            $sta = $eta = "";
            foreach ($train->subsequentCallingPoints->callingPointList->callingPoint as $callingPoint) {
                if ($callingPoint->crs === $filterCrs) {
                    $sta = $callingPoint->st;
                    $eta = $callingPoint->et;
                    break;
                }
            }
            $trains[] = [
                'departureLocation' => $response->GetStationBoardResult->locationName,
                'std' => $std,
                'etd' => $train->etd,
                'departurePlatform' => $train->platform,
                'arrivalLocation' => $response->GetStationBoardResult->locationName,
                'sta' => $sta,
                'eta' => $eta,
                'arrivalPlatform' => $response->GetStationBoardResult->platformAvailable,
                'duration' => $this->getDurationInMins($std, $sta),
            ];
        }
        return $trains;
    }

    private function getDurationInMins(string $dep, string $arr)
    {
        return (strtotime($arr) - strtotime($dep))/60;
    }
}