<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StatRepository extends EntityRepository
{
    public function getStat()
    {
        $stats = $this->findAll();
        return empty($stats) ? new Stat() : $stats[0];
    }

    public function incrementStat(): void
    {
        $this->createQueryBuilder('stat')
            ->update()
            ->set('stat.lastHit', ':now')
            ->set('stat.hitsCount', 'stat.hitsCount+1')
            ->getQuery()
            ->execute();
    }
}
