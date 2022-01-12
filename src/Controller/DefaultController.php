<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        // $store = new Store('/var/cache/');
        // $client = HttpClient::create();
        // $this->client = new CachingHttpClient($client, $store);
    }

    #[Route('/', name: 'default')]
    public function index(): Response
    {
        $request = Request::createFromGlobals();
        $queryIp = $request->query->get('ip');
        $ip = $queryIp ?? $request->getClientIp();

        $ipLocationKey = 'ff0dc6aefeed23e9ed7517f34831efab';
        $ipRequestQuery = http_build_query([
            'access_key' => $ipLocationKey,
        ]);

        $response = $this->client->request(
            'GET',
            'http://api.ipstack.com/' . $ip . '?' . $ipRequestQuery
        );
        $contentArray = $response->toArray();
        $latitude = $contentArray['latitude'];
        $longitude = $contentArray['longitude'];

        $weatherKey = '75644221927cf43372e9901b8ab3fce1';
        $requestQuery = http_build_query([
            'lat' => $latitude,
            'lon' => $longitude,
            'exclude' => implode(',', [
                'minutely',
                'hourly',
                'daily',
            ]),
            'appid' => $weatherKey,
        ]);
        $weatherRespone = $this->client->request(
            'GET',
            'https://api.openweathermap.org/data/2.5/onecall' . '?' . $requestQuery,
        );
        $weatherArray = $weatherRespone->toArray();

        $requestData = [
            // $statusCode,
            // $content,
            // $contentArray,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'weatherArray' => $weatherArray,
        ];

        return $this->json([
            'ip' => $ip,
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DefaultController.php',
            'requestData' => $requestData,
        ]);
    }
}
