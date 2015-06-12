<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class HighScoreController
 * @package AppBundle\Controller
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class HighScoreController extends Controller
{

    /**
     * @Route("/highestscores", name="_highest_scores")
     */
    public function highestScoresAction($limit = 10)
    {
        $highScoreRepo = $this->getDoctrine()->getRepository('AppBundle:HighScore');

        $highScores = $highScoreRepo->getHighScore($limit);

        return $this->render(':default/partial:highscore.html.twig', [
            'scores' => $highScores
        ]);
    }
}
