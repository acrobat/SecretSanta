<?php

namespace App\Query;

use Doctrine\DBAL\Driver\Connection;
use App\Entity\Participant;

class WishlistMailQuery
{
    /** @var Connection */
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * @param Participant $participant
     */
    public function countWishlistItemsOfParticipant(Participant $participant)
    {
        $query = $this->dbal->createQueryBuilder()
            ->select('count(w.id) AS wishlistItemCount')
            ->from('wishlist_item', 'w')
            ->where('w.participant_id = :participantId')
            ->setParameter('participantId', $participant->getId());

        return $query->execute()->fetchAll();
    }
}
