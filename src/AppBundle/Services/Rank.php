<?php

namespace AppBundle\Services;

use AppBundle\Repository\RankRepository;
use Doctrine\ORM\EntityManager;

class Rank
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getComputedRank($id)
    {
        $repository = $this->em->getRepository('AppBundle:Rank');
        /* @var $repository RankRepository */
        $rank = $repository->find($id);
        /* @var $rank \AppBundle\Entity\Rank */

        $query = $repository->createQueryBuilder('r')
            ->where('r.value > :value')
            ->addOrderBy('r.createDate', 'ASC')
            ->setParameter('value', $rank->getValue())
            ->getQuery();
        return count($query->getResult())+1;
    }

    public function getFirsts($limit)
    {
        $repository = $this->em->getRepository('AppBundle:Rank');
        /* @var $repository RankRepository */

        $query = $repository->createQueryBuilder('r')
            ->addOrderBy('r.value', 'DESC')
            ->addOrderBy('r.createDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery();
        return $query->getResult();
    }
}