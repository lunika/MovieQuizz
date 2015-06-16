<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PlayControllerTest
 * @package AppBundle\Tests\Controller
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class PlayControllerTest extends WebTestCase
{

    public function testSimpleLoad()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/play');

        $this->assertEquals(
            2,
            $crawler->filter('img')->count(),
            '/play must contain 2 images'
        );
    }

    public function testRightSubmission()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/play');

        $movieId = $crawler->filter('#quizz_movie')->attr('value');
        $actorId = $crawler->filter('#quizz_actor')->attr('value');

        $movieRepo = $client->getContainer()->get('doctrine')->getRepository('AppBundle:Movie');
        $personRepo = $client->getContainer()->get('doctrine')->getRepository('AppBundle:Person');

        $movie = $movieRepo->find($movieId);
        $person = $personRepo->find($actorId);

        if ($movie->isActor($person)) {
            $form = $crawler->selectButton('quizz_yes')->form();
        } else {
            $form = $crawler->selectButton('quizz_no')->form();
        }

        $client->submit($form);

        $request = $client->getRequest();
        $this->assertEquals('/play', $request->getPathInfo(), 'on a good submission, the player is redirected on /play');
    }

    public function testWrongSubmission()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/play');

        $movieId = $crawler->filter('#quizz_movie')->attr('value');
        $actorId = $crawler->filter('#quizz_actor')->attr('value');

        $movieRepo = $client->getContainer()->get('doctrine')->getRepository('AppBundle:Movie');
        $personRepo = $client->getContainer()->get('doctrine')->getRepository('AppBundle:Person');

        $movie = $movieRepo->find($movieId);
        $person = $personRepo->find($actorId);

        if ($movie->isActor($person)) {
            $form = $crawler->selectButton('quizz_no')->form();
        } else {
            $form = $crawler->selectButton('quizz_yes')->form();
        }

        $client->submit($form);

        $request = $client->getRequest();
        $this->assertEquals('/gameover', $request->getPathInfo(), 'on a wrong submission, player is redirected on /gameover');
    }

    public function testChangingSignature()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/play');

        $form = $crawler->selectButton('quizz_no')->form();
        $form->setValues([
            'quizz[movie]' => uniqid()
        ]);

        $client->submit($form);

        $request = $client->getRequest();
        $this->assertEquals('/gameover', $request->getPathInfo(), 'if form is modified the player is redirected to /gameover');
    }
}
