<?php

namespace App\Controller;

use App\Service\IPLocationService;
use App\Service\IPService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultController extends AbstractController
{
    private HttpClientInterface $client;

    private IPService $ip;

    private IPLocationService $ipLocation;

    public function __construct(IPService $ip, IPLocationService $ipLocation)
    {
        $store = new Store('/var/cache');
        $client = HttpClient::create();
        $client = new CachingHttpClient($client, $store);
        $this->client = $client;
        $this->ip = $ip;
        $this->ipLocation = $ipLocation;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[Route('/', name: 'default')]
    public function index(LoggerInterface $logger): Response
    {
        $request = Request::createFromGlobals();
        $queryIp = $request->query->get('ip');
        $ip = $queryIp ?? $this->ip->get($request);

        $this->ipLocation->getByIp($ip);
        $latitude = $this->ipLocation->getLatitude();
        $longitude = $this->ipLocation->getLongitude();

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
        try {
            $weatherResponse = $this->client->request(
                'GET',
                'https://api.openweathermap.org/data/2.5/onecall' . '?' . $requestQuery,
            );
            $weatherArray = $weatherResponse->toArray();
        } catch (Exception $e) {
            $logger->info($e->getMessage());
            $weatherArray = [];
        }

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
