<?php

namespace AppBundle\Tests\Tmdb;

use AppBundle\Tmdb\Client;

/**
 * Class Client
 * @package AppBundle\Tests\Tmdb
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URI = "http://private-anon-7938b87c3-themoviedb.apiary-mock.com/3/";

    public function testConnection()
    {
        $client = new Client('foo', 0, ['base_uri' => self::BASE_URI]);

        $response = $client->get('configuration');

        $this->assertEquals(200, $response->getStatusCode(), '/configuration must return a http code 200');

        $body =$response->getBody();
        $this->assertNotEmpty($body->getContents(), '/configuration returned body can\'t be empty');

        // for mock server url, it is X-Apiary-RateLimit-Remaining insead of X-RateLimit-Remaining
        $limit = $response->getHeader('X-Apiary-RateLimit-Remaining');
        $this->assertGreaterThan(0, $limit[0], '/configuration X-RateLimit limit must greater than 0');
    }
}
