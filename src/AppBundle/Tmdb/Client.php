<?php

namespace AppBundle\Tmdb;

use GuzzleHttp\Client as HttpClient;

/**
 * Class Client
 * @package AppBundle\Tmdb
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class Client
{
    protected $apiKey;

    protected $client;

    const BASE_URI = "https://api.themoviedb.org/3/";

    public function __construct($apiKey, $option = [])
    {
        $this->apiKey = $apiKey;

        $options = array_merge(['base_uri' => self::BASE_URI], $option);

        $this->client = new HttpClient(
            [
                'base_uri' => $options['base_uri']
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
        $options['query']['api_key'] = $this->apiKey;

        $this->client->get($uri, $options);
    }
}