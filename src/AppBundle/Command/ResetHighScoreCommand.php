<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        $repo = $this->getContainer()->get('doctrine')->getRepository('AppBundle:HighScore');
        $connection = $this->getContainer()->get('doctrine')->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('high_score'));

        $output->writeln(['', '<info>reset done !</info>']);

    }


}
