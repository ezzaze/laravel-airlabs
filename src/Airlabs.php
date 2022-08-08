<?php

namespace Ezzaze\Airlabs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\{Response, JsonResponse};
use Illuminate\Support\Facades\Cache;
use Nette\Utils\Json;

class Airlabs
{
    protected string $endpoint;
    protected string $base_url;
    protected string $result;
    protected array $queryAttributes = [];

    public function __construct()
    {
        $this->base_url = $this->getBaseUrl();
    }

    public function __call($name, $arguments = [])
    {
        $this->endpoint = $name;
        $this->queryAttributes = $arguments;
        return $this->getData();
    }

    /**
     * get the results of the api call
     *
     * @return void
     */
    private function getData(): array|JsonResponse
    {
        if ($content = Cache::get("airlabs.{$this->endpoint}")) {
            return $this->formatResult($content);
        }

        $client = new Client([
            'base_uri' => self::getBaseUrl(),
        ]);

        try {
            $res = $client->request('GET', $this->endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'api_key' => config('airlabs.api_key'),
                    ...$this->queryAttributes,
                ],
            ]);
            $content = Json::decode($res->getBody()->getContents());
            if (!isset($content->error)) {
                Cache::put("airlabs.{$this->endpoint}", $content->response, config('airlabs.cache.lifetime'));
                return $content->response;
            }
            return $content->error?->message;
        } catch (GuzzleException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get the base url of the airlabs api
     *
     * @return string
     */
    protected function getBaseUrl(): string
    {
        $version = self::getVersion();

        return "https://airlabs.co/api/{$version}/";
    }

    /**
     * get the current version ailabs api is on
     *
     * @return string
     */
    protected function getVersion(): string
    {
        return config('airlabs.version');
    }

    /**
     * Format the result of the api
     *
     * @param  array $result
     * @return array
     */
    private function formatResult(array $result): array
    {
        $collection = collect($result);
        foreach (collect($this->queryAttributes)->collapse() as $name => $value) {
            $collection = $collection->where($name, $value);
        }
        return $collection->values()->all();
    }
}
