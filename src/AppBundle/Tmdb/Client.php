<?php

namespace AppBundle\Tmdb;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Class Client
 * @package AppBundle\Tmdb
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class Client
{
    protected $apiKey;

    protected $client;

    protected $delay;

    const BASE_URI = "https://api.themoviedb.org/3/";
    const DEFAULT_DELAY = 250;

    public function __construct($apiKey, $delay, $option = [])
    {
        $this->apiKey = $apiKey;

        $this->delay = $delay;

        $options = array_merge(['base_uri' => self::BASE_URI], $option);

        $this->client = new HttpClient(
            [
                'base_uri' => $options['base_uri'],
                RequestOptions::HEADERS => ['Accept' => 'application/json']
            ]
        );
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($uri, array $options = [])
    {
        $options[RequestOptions::QUERY]['api_key'] = $this->apiKey;

        // little delay before sending the request for never reach the Request Rate Limit
        // http://docs.themoviedb.apiary.io/#introduction/request-rate-limiting
        $options[RequestOptions::DELAY] = $this->delay;

        return $this->client->get($uri, $options);
    }
}
