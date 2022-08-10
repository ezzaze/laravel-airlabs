<?php

namespace Ezzaze\Airlabs;

use Ezzaze\Airlabs\Exceptions\AirlabsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\Cache;
use Nette\Utils\Json;

class Airlabs
{
    protected $client;
    protected string $endpoint;
    protected string $base_url;
    protected string $result;
    protected array $queryAttributes = [];
    protected array $output = [];

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->getBaseUrl(),
        ]);
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
     * @return array|JsonResponse
     * @throws AirlabsException
     */
    private function getData(): array|JsonResponse
    {
        if ($this->ping()) {
            if (filter_var(config('airlabs.cache.enabled'), FILTER_VALIDATE_BOOL) === true) {
                if ($content = Cache::get("airlabs.{$this->endpoint}")) {
                    return $this->formatResult($content);
                }
            }

            try {
                $res = $this->client->request('GET', $this->endpoint, [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'api_key' => config('airlabs.api_key'),
                        ...$this->queryAttributes,
                    ],
                ]);
                $content = Json::decode($res->getBody()->getContents());
                if (isset($content->error)) {
                    $errorCode = $content->error->code ?? null;

                    throw AirlabsException::writeError($errorCode);
                }
                $this->output = $content->response;
                $this->handleCache();

                return $this->output;
            } catch (GuzzleException $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return [];
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

    /**
     * Handle the caching of the api results if enabled
     *
     * @return void
     */
    private function handleCache(): void
    {
        if (filter_var(config('airlabs.cache.enabled'), FILTER_VALIDATE_BOOL) === true) {
            Cache::put("airlabs.{$this->endpoint}", $this->output, config('airlabs.cache.lifetime'));
        }
    }

    /**
     * Ping the airlabs api to check if all is ok
     *
     * @return bool returns `true` if all is good
     * @throws AirlabsException
     */
    public function ping(): bool
    {
        $res = $this->client->request('GET', __FUNCTION__, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => [
                'api_key' => config('airlabs.api_key'),
                ...$this->queryAttributes,
            ],
        ]);
        $content = Json::decode($res->getBody()->getContents());
        if (isset($content->error)) {
            $errorCode = $content->error->code ?? null;

            throw AirlabsException::writeError($errorCode);
        }

        return true;
    }
}
