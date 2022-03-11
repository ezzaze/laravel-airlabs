<?php

namespace Ezzaze\Airlabs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;
use Nette\Utils\Json;

class Airlabs
{
    protected string $endpoint;
    protected string $base_url;
    protected array $queryAttributes = [];

    public function __construct()
    {
        $this->base_url = $this->getBaseUrl();
    }

    /**
     * query to retrieve the list of airports
     *
     * @return self
     */
    public function airports()
    {
        $this->endpoint = __FUNCTION__;

        return $this;
    }

    /**
     * query to retrieve the list of flight schedules
     *
     * @return self
     */
    public function schedules()
    {
        $this->endpoint = __FUNCTION__;

        return $this;
    }

    /**
     * query to retrieve the list of airlines
     *
     * @return self
     */
    public function airlines()
    {
        $this->endpoint = __FUNCTION__;

        return $this;
    }

    /**
     * query to retrieve the list of cities
     *
     * @return self
     */
    public function cities()
    {
        $this->endpoint = __FUNCTION__;

        return $this;
    }

    /**
     * query to retrieve the list of fleets
     *
     * @return self
     */
    public function fleets()
    {
        $this->endpoint = __FUNCTION__;

        return $this;
    }

    /**
     * query to retrieve the list of routes
     *
     * @return self
     */
    public function routes()
    {
        $this->endpoint = __FUNCTION__;

        return $this;
    }

    /**
     * query to retrieve the list of timezones
     *
     * @return self
     */
    public function timezones()
    {
        $this->endpoint = __FUNCTION__;

        return $this;
    }

    /**
     * set query params to fetch data
     *
     * @param  array $attributes
     * @return self
     */
    public function withQuery(array $attributes = [])
    {
        $this->queryAttributes = $attributes;

        return $this;
    }

    /**
     * get the results of the
     *
     * @return void
     */
    public function get()
    {
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
            //TODO: throw custom exception in case of an error with status 200
            return $content->response ?: $content->error->message;
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
        return "v" . config('airlabs.version');
    }
}
