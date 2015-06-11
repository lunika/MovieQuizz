<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $movieRepo = $this->getDoctrine()->getRepository('AppBundle:Movie');

        $movie = $movieRepo->find(329);

        echo $movie->getName()."<br>";

        $actors = $movie->getPersons();

        foreach ($actors as $actor) {
            echo $actor->getName()."<br>";
            if ($movie->isActor($actor)) {
                echo "yes";
            } else {
                echo "no";
            }
        }


        return $this->render('default/index.html.twig');
    }
}
