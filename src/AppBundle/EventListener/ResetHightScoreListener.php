<?php

namespace AppBundle\EventListener;

use AppBundle\AppEvents;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ResetHightScoreListener
 * @package AppBundle\EventListener
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class ResetHightScoreListener implements EventSubscriberInterface
{

    protected $doctrine;

    public function __construct(Registry $doctine)
    {
        $this->doctrine = $doctine;
    }

    public function resetHighScore(Event $event)
    {
        $connection = $this->doctrine->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('high_score'));
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            AppEvents::RESET_HIGHSCORE => 'resetHighScore'
        ];
    }
}
