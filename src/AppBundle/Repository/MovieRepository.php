<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MovieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MovieRepository extends EntityRepository
{

    /**
     * choose randomly a movie in database
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getRandomMovie()
    {
        $count = $this->createQueryBuilder('m')->select('COUNT(m.id)')->getQuery()->getSingleScalarResult();

        $offset = intval(rand(0, $count-1));

        return $this->createQueryBuilder('m')
            ->setMaxResults(1)
            ->setFirstResult($offset)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
