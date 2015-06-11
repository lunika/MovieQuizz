<?php

namespace AppBundle\Command;

use AppBundle\Entity\Movie;
use AppBundle\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FetchAllCommand
 * @package AppBundle\Command
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class FetchAllCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:fetch-all')
            ->setDescription('save in database popular movies and linked actors. This can take several minutes')
            ->addOption(
                'page',
                null,
                InputOption::VALUE_OPTIONAL,
                'number of page to fetch',
                1
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("start saving movies, this can take several minutes");
        $em = $this->getContainer()->get('doctrine')->getManager();
        $movies = $this->fetchMovies($input, $output);

        foreach ($movies as $movie) {
            if (false === $this->movieExists($movie['id'])) {
                $movieModel = new Movie();
                $movieModel
                    ->setId($movie['id'])
                    ->setName($movie['original_title'])
                    ->setPicture($movie['poster_path']);

                $this->fetchCast($movieModel, $em);
                $em->persist($movieModel);
                $em->flush();
                $output->writeln(sprintf('<info>Movie %s saved', $movie['original_title']));
            } else {
                $output->writeln(sprintf('<info>Skip movie %s</info>', $movie['original_title']));
            }
        }


        $output->writeln('<info>Ended successfully</info>');
    }

    protected function fetchCast(Movie $movie, EntityManagerInterface $em)
    {
        /** @var \AppBundle\Tmdb\Repository\Movie $movieRepo */
        $movieRepo = $this->getContainer()->get('app.tmdb_repository.movie');

        $response = $movieRepo->getCredits($movie->getId());
        $body = json_decode($response->getBody(), true);
        $persons = $body['cast'];

        foreach ($persons as $person) {
            $personRepo = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Person');

            if (null === $personModel = $personRepo->findOneById($person['id'])) {
                $personModel = new Person();
                $personModel
                    ->setId($person['id'])
                    ->setName($person['name'])
                    ->setPicture($person['profile_path']);

                if (false === $movie->isActor($personModel)) {
                    $em->persist($personModel);
                }
            }

            $movie->addPerson($personModel);
        }
        $this->verifyRatingLimit($response);

    }

    protected function fetchMovies(InputInterface $input, OutputInterface $output)
    {
        /** @var \AppBundle\Tmdb\Repository\Movie $movieRepo */
        $movieRepo = $this->getContainer()->get('app.tmdb_repository.movie');
        $pages = $input->getOption('page');

        $movies = [];

        for ($i=1; $i <= $pages; $i ++) {
            $response = $movieRepo->getPopular(['page' => $i]);
            $body = json_decode($response->getBody(), true);
            $movies = array_merge($movies, $body['results']);
            $this->verifyRatingLimit($response);
        }

        return $movies;
    }

    protected function movieExists($id)
    {
        $repo = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Movie');

        return $repo->findOneById($id) !== null;
    }

    /**
     * It is not possible to request the API more than 40 times in 10 seconds.
     * If limit is reached, wait 2 seconds.
     *
     * @param ResponseInterface $response
     */
    protected function verifyRatingLimit(ResponseInterface $response)
    {
        $limit = $response->getHeader('X-RateLimit-Remaining');

        if ($limit[0] == 0) {
            sleep(2);
        }
    }
}
