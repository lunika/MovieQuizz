<?php

namespace AppBundle\Controller;

use AppBundle\Entity\HighScore;
use AppBundle\Form\Type\HighScoreType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/highscore", name="highscore_save")
     * @Method("POST")
     * @param Request $request
     */
    public function saveAction(Request $request)
    {
        $form = $this->createForm(new HighScoreType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $highScore = new HighScore();
            $session = $request->getSession();

            $duration = $session->get('end_time') - $session->get('start_time');

            $highScore
                ->setName($form->get('name')->getData())
                ->setScore($request->getSession()->get('score'))
                ->setDuration($duration)
            ;

            $em = $this->getDoctrine()->getManager();
            $em->persist($highScore);
            $em->flush();

            return $this->redirectToRoute("highscore_display");
        } else {
            return $this->forward("AppBundle:Play:gameOver", [], ['error' => 1]);
        }
    }

    /**
     * @Route("/highscore", name="highscore_display")
     * @Method("GET")
     */
    public function displayAction()
    {
        return $this->render(":default:highscore.html.twig");
    }
}
