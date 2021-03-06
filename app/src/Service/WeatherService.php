<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private WeatherServiceConfig $config;

    private HttpClientInterface $client;

    private LoggerInterface $logger;

    // TODO: Move to .ENV
    private const API_KEY = '75644221927cf43372e9901b8ab3fce1';
    private const API_HOST = 'https://api.openweathermap.org/data/2.5/onecall';

    public function __construct(WeatherServiceConfig $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $store = new Store('../var/cache123');
        $client = HttpClient::create();
        $client = new CachingHttpClient($client, $store);
        $this->client = $client;
    }

    public function get(WeatherServiceConfig $config): array
    {
        $this->config = $config;

        $requestQuery = http_build_query([
            'lat' => $this->config->latitude,
            'lon' => $this->config->longitude,
            'exclude' => implode(',', [
                'minutely',
                'hourly',
                'daily',
            ]),
            'appid' => self::API_KEY,
        ]);
        $weatherArray = [];
        try {
            $weatherResponse = $this->client->request(
                'GET',
                self::API_HOST . '?' . $requestQuery,
            );
            $weatherArray = $weatherResponse->toArray();

            echo '<pre>';
            var_dump($weatherArray);
            die;
        } catch (
            TransportExceptionInterface|
            DecodingExceptionInterface|
            ClientExceptionInterface|
            RedirectionExceptionInterface|
            ServerExceptionInterface $e
        ) {
            $this->logger->error($e->getMessage());
        }
        return $weatherArray;
    }
}