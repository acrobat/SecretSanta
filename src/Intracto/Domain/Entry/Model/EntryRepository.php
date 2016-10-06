<?php

namespace Intracto\Domain\Entry\Model;

use Doctrine\ORM\EntityRepository;

/**
 * Class EntryRepository.
 */
class EntryRepository extends EntityRepository
{
    /**
     * @param \DateTime $startDate
     *
     * @return Entry[]
     */
    public function findAfter(\DateTime $startDate)
    {
        $query = $this->_em->createQuery('
            SELECT entry
            FROM IntractoSecretSantaBundle:Entry entry
            JOIN entry.pool pool
            JOIN entry.entry peer
            WHERE pool.sentdate >= :startDate
              AND peer.wishlist IS NOT NULL
        ');
        $query->setParameter('startDate', $startDate, \Doctrine\DBAL\Types\Type::DATETIME);

        return $query->getResult();
    }
}
