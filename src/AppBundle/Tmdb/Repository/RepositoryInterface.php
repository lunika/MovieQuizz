<?php
namespace AppBundle\Tmdb\Repository;

use AppBundle\Tmdb\Client;

/**
 * Interface RepositoryInterface
 * @package AppBundle\Tmdb\Repository
 * @author Manuel Raynaud <manu@raynaud.io>
 */
interface RepositoryInterface
{
    public function __construct(Client $client);
}
