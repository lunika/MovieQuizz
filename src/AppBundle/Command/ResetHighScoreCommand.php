<?php

namespace AppBundle\Command;

use AppBundle\AppEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ResetHighScoreCommand
 * @package AppBundle\Command
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class ResetHighScoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:reset-highscore')
            ->setDescription('reset all saved high score');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['<info>removing all records</info>']);

        $this->getContainer()->get('event_dispatcher')->dispatch(AppEvents::RESET_HIGHSCORE, new Event());

        $output->writeln(['', '<info>reset done !</info>']);
    }
}
