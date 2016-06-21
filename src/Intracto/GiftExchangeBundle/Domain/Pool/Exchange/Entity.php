<?php
namespace Intracto\GiftExchangeBundle\Domain\Pool\Exchange;

use Doctrine\ORM\Mapping as ORM;
use Intracto\GiftExchangeBundle\Domain\Pool\Entity as Pool;
use Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity as Participant;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gx_exchange")
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Entity", inversedBy="exchanges")
     */
    private $pool;

    /**
     * @ORM\OneToOne(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity")
     * @ORM\JoinColumn(name="giver_id", referencedColumnName="id")
     */
    private $giver;

    /**
     * @ORM\OneToOne(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     */
    private $receiver;

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
     * @return Participant
     */
    public function getGiver()
    {
        return $this->giver;
    }

    /**
     * @return Participant
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param Pool $pool
     * @param Participant $giver
     * @param Participant $receiver
     * @return Entity
     */
    static public function create(Pool $pool, Participant $giver, Participant $receiver)
    {
        $exchange = new self();
        $exchange->pool = $pool;
        $exchange->giver = $giver;
        $exchange->receiver = $receiver;

        $exchange->validate();

        return $exchange;
    }

    public function validate()
    {
        // Validate this object
        // Throw exception if not valid
    }
}