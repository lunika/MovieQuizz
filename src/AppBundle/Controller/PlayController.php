<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PlayController
 * @package AppBundle\Controller
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class PlayController extends Controller
{

    /**
     * @Route("/play", name="play")
     */
    public function play()
    {
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


        return $this->render('default/play.html.twig', [
            'movie' => $movie,
            'actor' => $actor
        ]);
    }
}
