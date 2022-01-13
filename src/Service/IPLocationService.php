<?php

namespace App\Service;

use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class IPLocationService
{
    private HttpClientInterface $client;

    // TODO: Move key no .ENV
    private const API_KEY = 'ff0dc6aefeed23e9ed7517f34831efab';
    private const API_HOST = 'http://api.ipstack.com/';

    private array $responseArray;

    public function __construct()
    {
        $store = new Store('/var/cache');
        $client = HttpClient::create();
        $client = new CachingHttpClient($client, $store);
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getByIp(string $ip): ResponseInterface
    {
        $ipRequestQuery = http_build_query([
            'access_key' => self::API_KEY,
        ]);
        $response = $this->client->request(
            'GET',
            self::API_HOST . $ip . '?' . $ipRequestQuery
        );
        $this->responseArray = $response->toArray();
        return $response;
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getLatitude()
    {
        return $this->responseArray['latitude'];
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getLongitude()
    {
        return $this->responseArray['longitude'];
    }
}
