<?php

namespace App\Controller;

use App\Service\IPLocationService;
use App\Service\IPService;
use App\Service\WeatherService;
use App\Service\WeatherServiceConfig;
use Doctrine\DBAL\ConnectionException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DefaultController extends AbstractController
{
    private IPService $ip;

    private IPLocationService $ipLocation;

    private WeatherService $weather;

    public function __construct(IPService $ip, IPLocationService $ipLocation, WeatherService $weather)
    {
        $this->ip = $ip;
        $this->ipLocation = $ipLocation;
        $this->weather = $weather;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[Route('/', name: 'default')]
    public function index(): Response
    {
        $request = Request::createFromGlobals();
        $queryIp = $request->query->get('ip');
        $ip = $queryIp ?? $this->ip->get($request);

        try {
            $this->ipLocation->getByIp($ip);
        }
        catch (Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }
        $weatherServiceConfig = new WeatherServiceConfig();
        $weatherServiceConfig->latitude = $this->ipLocation->getLatitude();
        $weatherServiceConfig->longitude = $this->ipLocation->getLongitude();
        $weatherData = $this->weather->get($weatherServiceConfig);

        return $this->json([
            'weather' => $weatherData,
        ]);
    }
}
