<?php
namespace Intracto\GiftExchangeBundle\Domain\Pool;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gx_pool")
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Exchange\Entity", mappedBy="pool")
     */
    private $exchanges;

    /**
     * @ORM\OneToMany(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity", mappedBy="pool")
     */
    private $participants;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->participants = new ArrayCollection();
        $this->exchanges = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @return ArrayCollection
     */
    public function getExchanges()
    {
        return $this->exchanges;
    }
}