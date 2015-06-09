<?php

namespace AppBundle\Tmdb\Repository;

use AppBundle\Tmdb\Client;

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
        $options = [];
        $options['query'] = array_merge([
            'page' => 1,
            'language' => 'en'
        ], $options);

        if ($options['query']['page'] < 0 || $options['query']['page'] > 1000) {
            throw new \RuntimeException(sprintf('page number for getPopulat method must be between 1 and 1000. %d given', $options['query']['page']));
        }

        return $this->client->get('movie/popular', $options);
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
