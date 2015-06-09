<?php

namespace AppBundle\Tmdb\Repository;

use AppBundle\Tmdb\Client;
use GuzzleHttp\RequestOptions;

/**
 * Class Movie
 * @package AppBundle\Tmdb\Repository
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class Movie implements RepositoryInterface
{
    /**
     * @var \AppBundle\Tmdb\Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $options optional parameters used for popular movies :
     *  - page : Minimum 1, maximum 1000.
     *  - language : ISO 639-1 code.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getPopular($options = array())
    {
        $parameters = [];
        $parameters[RequestOptions::QUERY] = array_merge([
            'page' => 1,
            'language' => 'en'
        ], $options);

        if ($parameters['query']['page'] < 0 || $parameters['query']['page'] > 1000) {
            throw new \RuntimeException(sprintf('page number for getPopulat method must be between 1 and 1000. %d given', $parameters['query']['page']));
        }

        return $this->client->get('movie/popular', $parameters);
    }

    /**
     * @param $id
     * @param array $options optional parameters used for movie's credits :
     *  - append_to_response : see http://docs.themoviedb.apiary.io/#introduction/appending-responses
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getCredits($id, $options = array())
    {
        $uri = sprintf("movie/%s/credits", $id);

        $options['query'] = $options;

        return $this->client->get($uri, $options);
    }
}
