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

    /**
     * check if once a party loaded, there are 2 images.
     */
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

    /**
     * Test what append when the player submit a good answer
     */
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

    /**
     * Test what append when a player submit a wrong answer
     */
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

    /**
     * change the signature and then submit the form.
     */
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

    /**
     * Test a party without good answer
     */
    public function testGameOverWithoutScore()
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

        $crawler = $client->submit($form);

        $request = $client->getRequest();
        $this->assertEquals('/gameover', $request->getPathInfo(), 'on a wrong submission, player is redirected on /gameover');

        $statement = $crawler->filter("#score")->html();

        $this->assertRegExp('/0 good answer in \d{2}:\d{2}:\d{2}/', $statement, 'without good response the score must be 0');

        $this->assertEquals(0, $crawler->filter('form[name=high_score]')->count(), 'form for highscore must be missing');
    }

    /**
     * Test a party with a new high score
     */
    public function testNewHighScore()
    {
        $client = static::createClient();
        $client->followRedirects();

        $movieRepo = $client->getContainer()->get('doctrine')->getRepository('AppBundle:Movie');
        $personRepo = $client->getContainer()->get('doctrine')->getRepository('AppBundle:Person');
        $highScoreRepo = $client->getContainer()->get('doctrine')->getRepository('AppBundle:HighScore');

        $highestScore = $highScoreRepo->getHighScore(1);
        if (empty($highestScore)) {
            $newHighscore = 2;
        } else {
            $newHighscore = $highestScore->getScore()+1;
        }

        $crawler = $client->request('GET', '/play');

        for ($i = 0; $i <= $newHighscore; $i++) {
            $movieId = $crawler->filter('#quizz_movie')->attr('value');
            $actorId = $crawler->filter('#quizz_actor')->attr('value');

            $movie = $movieRepo->find($movieId);
            $person = $personRepo->find($actorId);

            if ($movie->isActor($person)) {
                $form = $crawler->selectButton('quizz_yes')->form();
            } else {
                $form = $crawler->selectButton('quizz_no')->form();
            }

            $crawler = $client->submit($form);

            $request = $client->getRequest();
            $this->assertEquals('/play', $request->getPathInfo(), 'on a good submission, the player is redirected on /play');
        }

        $movieId = $crawler->filter('#quizz_movie')->attr('value');
        $actorId = $crawler->filter('#quizz_actor')->attr('value');

        $movie = $movieRepo->find($movieId);
        $person = $personRepo->find($actorId);

        if ($movie->isActor($person)) {
            $form = $crawler->selectButton('quizz_no')->form();
        } else {
            $form = $crawler->selectButton('quizz_yes')->form();
        }

        $crawler = $client->submit($form);

        $request = $client->getRequest();
        $this->assertEquals('/gameover', $request->getPathInfo(), 'on a wrong submission, player is redirected on /gameover');

        $statement = $crawler->filter("#score")->html();

        $this->assertRegExp('/\d+ good answers in \d{2}:\d{2}:\d{2}/', $statement, 'without good response the score must be 0');

        $this->assertEquals(1, $crawler->filter('form[name=high_score]')->count(), 'form for highscore must be present');
    }
}
