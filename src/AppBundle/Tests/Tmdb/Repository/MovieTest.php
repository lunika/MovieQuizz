<?php

namespace AppBundle\Tests\Tmdb\Repository;

use AppBundle\Tests\Tmdb\ClientTest;
use AppBundle\Tmdb\Client;
use AppBundle\Tmdb\Repository\Movie;

/**
 * Class MovieTest
 * @package AppBundle\Tests\Tmdb\Repository
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class MovieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AppBundle\Tmdb\Repository\Movie
     */
    protected $repository;

    public function setUp()
    {
        $client = new Client('foo', 0, ['base_uri' => ClientTest::BASE_URI]);

        $this->repository = new Movie($client);
    }

    public function testPopularWithoutParameters()
    {
        $response = $this->repository->getPopular();
        $body = $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), "/movie/popular http status code must be 200");
        $this->assertNotEmpty($body->getContents(), "/movie/popular response body can't be empty");

        $body = json_decode($body, true);

        $this->assertArrayHasKey('page', $body, '/movie/popular body must contain page parameter');
        $this->assertArrayHasKey('results', $body, '/movie/popular body must contain results parameter');
        $this->assertEquals(1, $body['page'], '/movie/popular page parameter must equal 1');
        $this->assertGreaterThan(0, count($body['results']), '/movie/popular results parameter can\'t be empty');
    }

    public function testGetCredits()
    {
        //https://www.themoviedb.org/movie/49051-the-hobbit-an-unexpected-journey
        $response = $this->repository->getCredits(40951);
        $body = $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), '/movie/{id}/credits http status code must be 200');
        $this->assertNotEmpty($body->getContents(), "/movie/{id}/credits response body can't be empty");

        $body = json_decode($body, true);

        $this->assertArrayHasKey('cast', $body, '/movie/{id}/credits must contain cast parameter');
        $this->assertArrayHasKey('crew', $body, '/movie/{id}/credits must contain crew parameter');
        $this->assertNotEmpty($body['cast'], '/movie/{id}/credits cast parameter can\'t be empty');
    }
}
