<?php

declare(strict_types=1);

namespace App\Form\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participant;
use App\Entity\WishlistItem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class WishlistFormHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(FormInterface $form, Request $request): bool
    {
        /** @var Participant $participant */
        $participant = $form->getData();

        if (!$request->isMethod('POST')) {
            return false;
        }

        $currentWishlistItems = $this->extractCurrentWishlistItems($participant);

        if (!$form->handleRequest($request)->isValid()) {
            return false;
        }

        // Process incoming data
        $this->saveNewItems($participant);
        $this->deleteMissingItems($participant, $currentWishlistItems);

        // Mark the participant wishlist as changed
        $participant->setWishlistUpdated(true);
        $participant->setWishlistUpdatedTime(new \DateTime());

        $this->em->persist($participant);
        $this->em->flush();

        return true;
    }

    private function extractCurrentWishlistItems(Participant $participant): ArrayCollection
    {
        $currentWishlistItems = new ArrayCollection();

        /** @var WishlistItem $item */
        foreach ($participant->getWishlistItems() as $item) {
            $currentWishlistItems->add($item);
        }

        return $currentWishlistItems;
    }

    private function saveNewItems(Participant $participant)
    {
        $newWishlistItems = $participant->getWishlistItems();

        foreach ($newWishlistItems as $item) {
            $item->setParticipant($participant);
            $this->em->persist($item);
        }
    }

    private function deleteMissingItems(Participant $participant, ArrayCollection $currentWishlistItems)
    {
        $newWishlistItems = $participant->getWishlistItems();

        foreach ($currentWishlistItems as $item) {
            if (!$newWishlistItems->contains($item)) {
                $this->em->remove($item);
            }
        }
    }
}
