<?php

namespace AppBundle\Controller;

use AppBundle\Entity\HighScore;
use AppBundle\Form\Type\HighScoreType;
use AppBundle\Form\Type\QuizzType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class PlayController
 * @package AppBundle\Controller
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class PlayController extends Controller
{

    /**
     * @Route("/play", name="play")
     * @Method("GET")
     */
    public function playAction(Request $request)
    {
        $this->initializeParty($request->getSession());
        $movieRepo = $this->getDoctrine()->getRepository('AppBundle:Movie');

        $movie = $movieRepo->getRandomMovie();

        $inMovie = rand(0, 1);

        if ($inMovie) {
            $persons = $movie->getPersons()->toArray();
            $actor = $persons[array_rand($persons)];
        } else {
            $personRepo = $this->getDoctrine()->getRepository('AppBundle:Person');

            $actor = $personRepo->getRandomPerson($movie->getId());
        }

        $form = $this->createForm(
            new QuizzType(),
            [
                'movie' => $movie->getId(),
                'actor' => $actor->getId()
            ],
            [
                'action' => $this->generateUrl('process_play'),
            ]
        );


        return $this->render('default/play.html.twig', [
            'movie' => $movie,
            'actor' => $actor,
            'form' => $form->createView(),
            'score' => $request->getSession()->get('score', 0)
        ]);
    }

    /**
     * @Route("/play", name="process_play")
     * @Method("POST")
     */
    public function processPlayAction(Request $request)
    {
        $form = $this->createForm(new QuizzType());
        $form->handleRequest($request);
        $session = $request->getSession();

        if ($form->isValid()) {
            $movie = $this->getDoctrine()->getRepository('AppBundle:Movie')->find(
                $form->get('movie')->getData()
            );

            $actor = $this->getDoctrine()->getRepository('AppBundle:Person')->find(
                $form->get('actor')->getData()
            );

            $inMovie = $movie->isActor($actor);
            if (($inMovie && $form->get('yes')->isClicked()) || (!$inMovie && $form->get('no')->isClicked())) {
                $this->addScore($session, $session->get('score'));
                return $this->redirectToRoute('play');
            } else {
                $this->endParty($session);
                return $this->redirectToRoute('gameover');
            }
        } else {
            $this->endParty($session);
            return $this->redirectToRoute('gameover');
        }
    }

    /**
     * @Route("/gameover", name="gameover")
     */
    public function gameOverAction(Request $request)
    {
        $session = $request->getSession();

        $start = $session->get('start_time');
        $end = $session->get('end_time');
        $diff = $start->diff($end);
        $parameters = [
            'score' => $request->getSession()->get('score', 0),
            'duration' => $diff->format('%H:%I:%S')
        ];

        $parameters = $this->checkHighScore($request->getSession()->get('score', 0), $parameters);

        return $this->render('default/end.html.twig', $parameters);
    }

    protected function checkHighScore($score, $parameters)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:HighScore');

        if ($repo->isInHighScore($score)) {
            $form = $this->createForm(new HighScoreType(), [], ['action' => $this->generateUrl('highscore_save')]);
            $parameters['form'] = $form->createView();
        }

        return $parameters;
    }

    /**
     * increment the current score
     *
     * @param SessionInterface $session
     * @param $score
     */
    protected function addScore(SessionInterface $session, $score)
    {
        $session->set('score', ++$score);
    }

    /**
     * The party ended
     *
     * @param SessionInterface $session
     */
    protected function endParty(SessionInterface $session)
    {
        $session->set('end_time', new \DateTime());
        $session->set('inParty', false);
    }

    /**
     * When a new party begins, initialize parameters in session
     *
     * @param SessionInterface $session
     */
    protected function initializeParty(SessionInterface $session)
    {
        if (false === $session->get('inParty', false)) {
            $session->set('inParty', true);
            $session->set('score', 0);
            $session->set('start_time', new \DateTime());
        }
    }
}
