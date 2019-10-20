<?php

namespace AppBundle\Service;

use AppBundle\Model\Alert;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;

class FirebaseService
{
    /** @var Client */
    private $client;

    public function __construct(string $gcmApiKey)
    {
        $this->client = new Client([
            RequestOptions::HEADERS => [
                'Authorization' => 'key='.$gcmApiKey,
            ],
        ]);
    }

    /**
     * Notifies devices about alerts
     * @param array $regIds
     * @param Alert[] $alerts
     * @return bool
     */
    public function notifyAlerts(array $regIds, array $alerts): bool
    {
        $alertsArray = array_map(static function(Alert $alert) {
            return $alert->toArray();
        }, $alerts);

        return $this->send($regIds, [
            'alerts' => $alertsArray
        ]);
    }

    public function test(array $regIds): bool
    {
        return $this->send($regIds, ['test' => true]);
    }

    private function send(array $regIds, array $payload): bool
    {
        $results = [];

        foreach ($regIds as $regId) {
            try {
                $this->client->post('https://fcm.googleapis.com/fcm/send', [
                    RequestOptions::JSON => [
                        'to' => $regId,
                        'data' => ['test' => true],
                    ],
                ]);

                $results[] = true;
            } catch (ClientException|ServerException $ex) {
                $results[] = false;
            }
        }

        return count(array_filter($results)) === count($results);
    }
}
