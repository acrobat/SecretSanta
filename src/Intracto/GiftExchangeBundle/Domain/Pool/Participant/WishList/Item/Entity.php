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

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="smallint")
     */
    private $rank;


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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param WishList $wishList
     * @param string $description
     * @param string $image
     * @param int $rank
     * @return Entity
     */
    static public function create(WishList $wishList, $description, $image, $rank)
    {
        $item = new self();
        $item->wishList = $wishList;
        $item->description = $description;
        $item->image = $image;
        $item->rank = $rank;

        $item->validate();

        return $item;
    }

    public function validate()
    {
        // Validate this object
        // Throw exception if not valid
    }
}