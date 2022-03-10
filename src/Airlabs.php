<?php

namespace Ezzaze\Airlabs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class Airlabs
{
    protected string $endpoint;
    protected string $base_url;

    function __construct()
    {
        $this->base_url = $this->getBaseUrl();
    }

    public function airports(array $params = [])
    {
        $client = new Client([
            'base_uri' => self::getBaseUrl(),
        ]);

        try {
            $result = $client->request('GET', __FUNCTION__, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'api_key' => config('airlabs.api_key'),
                ],
            ]);

            return response()->json($result->getBody()->getContents(), $result->getStatusCode());
        } catch (GuzzleException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function getBaseUrl()
    {
        $version = self::getVersion();

        return "https://airlabs.co/api/{$version}/";
    }

    protected function getVersion()
    {
        return "v" . env('airlabs.version');
    }
}
