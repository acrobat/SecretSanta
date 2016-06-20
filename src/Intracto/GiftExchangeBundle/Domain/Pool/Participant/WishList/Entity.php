<?php
namespace Intracto\GiftExchangeBundle\Domain\Pool\Participant\WishList;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity as Participant;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gx_wish_list")
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\WishList\Item\Entity", mappedBy="wishList")
     */
    private $items;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Participant
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }
}