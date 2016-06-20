<?php
namespace Intracto\GiftExchangeBundle\Domain\Pool\Participant\WishList\Item;

use Doctrine\ORM\Mapping as ORM;
use Intracto\GiftExchangeBundle\Domain\Pool\Participant\WishList\Entity as WishList;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gx_wish_list_item")
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\WishList\Entity", inversedBy="items")
     */
    private $wishList;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return WishList
     */
    public function getWishList()
    {
        return $this->wishList;
    }
}