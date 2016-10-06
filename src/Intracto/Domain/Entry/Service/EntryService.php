<?php

namespace Intracto\Domain\Entry\Service;

use Doctrine\ORM\EntityManager;
use Intracto\SecretSantaBundle\Entity\Pool;

class EntryService
{
    /**
     * @var EntityManager
     */
    public $em;

    /**
     * @var Shuffler
     */
    public $entryShuffler;

    /**
     * @param EntityManager $em
     * @param Shuffler      $entryShuffler
     */
    public function __construct(EntityManager $em, Shuffler $entryShuffler)
    {
        $this->em = $em;
        $this->entryShuffler = $entryShuffler;
    }

    /**
     * Shuffles all entries for pool and save result to each entry.
     *
     * @param Pool $pool
     *
     * @return bool
     */
    public function shuffleEntries(Pool $pool)
    {
        //Validator should already have shuffled it.
        if (!$shuffled = $this->entryShuffler->shuffleEntries($pool)) {
            return false;
        }

        foreach ($pool->getEntries() as $key => $entry) {
            $match = $shuffled[$key];
            $entry->setEntry($match)
                ->setUrl(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));

            $this->em->persist($entry);
        }

        $this->em->flush();
    }
}
