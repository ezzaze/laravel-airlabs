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
    protected Client $client;
    protected string $endpoint;
    protected string $base_url;
    protected string $result;
    protected array $queryAttributes = [];
    protected array $output = [];
    protected bool $verifyPeer = true;
    protected bool $cacheEnabled = false;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->getBaseUrl(),
        ]);
        $this->cacheEnabled = filter_var(config('airlabs.cache.enabled'), FILTER_VALIDATE_BOOL);
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
            if ($this->cacheEnabled === true) {
                if ($this->output = Cache::get("airlabs.{$this->endpoint}.{$this->serializeQueryAttributes()}", [])) {
                    return $this->formatResult($this->output);
                }
            }

            try {
                $res = $this->client->request('GET', $this->endpoint, [
                    'verify' => $this->verifyPeer,
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'query' => collect($this->queryAttributes)
                        ->push(['api_key' => config('airlabs.api_key')])
                        ->collapse()
                        ->toArray(),
                ]);
                $content = Json::decode($res->getBody()->getContents());
                if (isset($content->response)) {
                    $this->output = $content->response;
                    $this->handleCache();

                    return $this->formatResult($this->output);
                }

                throw AirlabsException::writeError($content->error);
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
        if ($this->cacheEnabled === true) {
            Cache::put("airlabs.{$this->endpoint}.{$this->serializeQueryAttributes()}", $this->output, config('airlabs.cache.lifetime'));
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
        try {
            $res = $this->client->request('GET', __FUNCTION__, [
                'verify' => $this->verifyPeer,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'api_key' => config('airlabs.api_key'),
                    ...$this->queryAttributes,
                ],
            ]);
            $content = Json::decode($res->getBody()->getContents());
            if (isset($content->response)) {
                return true;
            }

            throw AirlabsException::writeError($content->error);
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * Set if SSL verification of the peer is necessary or not (this is insecure)
     *
     * @param  bool $value
     * @return self
     */
    public function verifyPeer(bool $value = true): self
    {
        $this->verifyPeer = $value;

        return $this;
    }

    /**
     * Serializing the query params received for caching purposes
     *
     * @return string
     */
    private function serializeQueryAttributes(): string
    {
        return md5(collect($this->queryAttributes)
            ->collapse()
            ->map(fn ($value) => strtolower($value))
            ->sortKeys()
            ->toJson());
    }
}
