<?php

namespace Ezzaze\Airlabs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class Airlabs
{
    protected static string $endpoint;

    public static function airports(array $params = [])
    {
        self::$endpoint = __FUNCTION__;

        $client = new Client([
            'base_uri' => self::getBaseUrl()
        ]);

        try {
            $result = $client->request('GET', self::$endpoint, [
                'headers' => [
                    'Accept' => 'application/json'
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

    protected static function getBaseUrl()
    {
        $version = self::getVersion();
        return "https://airlabs.co/api/{$version}/";
    }

    protected static function getVersion()
    {
        return "v" . env('airlabs.version');
    }
}
