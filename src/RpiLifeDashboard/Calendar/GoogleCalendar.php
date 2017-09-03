<?php


namespace RpiLifeDashboard\Calendar;


class GoogleCalendar
{
    const APPLICATION_NAME      = 'Rpi Life Dashboard';
    const CREDENTIALS_PATH      = __DIR__ . '/../../../secrets/calendar-php-quickstart.json';
    const CLIENT_SECRET_PATH    = __DIR__ . '/../../../secrets/client_secret.json';

    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * @var \Google_Service_Calendar
     */
    private $service;

    public function __construct()
    {
        $this->client = $this->getClient();
        $this->service = new \Google_Service_Calendar($this->client);
    }

    /**
     * @link https://developers.google.com/google-apps/calendar/quickstart/php
     * @return \Google_Client
     */
    protected function getClient() {

        $client = new \Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        //$client->setScopes(SCOPES);
        $client->setAuthConfig(self::CLIENT_SECRET_PATH);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        //$credentialsPath = expandHomeDirectory(self::CREDENTIALS_PATH);
        $credentialsPath = self::CREDENTIALS_PATH;
        if (file_exists(self::CREDENTIALS_PATH)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    public function listEvents()
    {
        $calendarId = 'primary';
        $optParams = array(
            'maxResults' => 20,
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => date('c'),
            'timeMax' => date('c', time() + 7 * 24 * 60 * 60), // 1 week
        );
        $results = $this->service->events->listEvents($calendarId, $optParams);

        return $results->getItems();
    }


}