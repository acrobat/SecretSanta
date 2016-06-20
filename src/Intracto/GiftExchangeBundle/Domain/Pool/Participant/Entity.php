<?php
namespace Intracto\GiftExchangeBundle\Domain\Pool\Participant;

use Doctrine\ORM\Mapping as ORM;
use Intracto\GiftExchangeBundle\Domain\Pool\Entity as Pool;
use Intracto\GiftExchangeBundle\Domain\Pool\Participant\WishList\Entity as WishList;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gx_participant")
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Entity", inversedBy="participants")
     */
    private $pool;

    /**
     * @ORM\OneToOne(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\WishList\Entity")
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
     * @return Pool
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * @return WishList
     */
    public function getWishList()
    {
        return $this->wishList;
    }
}