<?php
namespace Intracto\GiftExchangeBundle\Domain\Pool;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity as Participant;
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
     * @ORM\OneToOne(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;
    
    /**
     * @ORM\OneToMany(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Exchange\Entity", mappedBy="pool")
     */
    private $exchanges;

    /**
     * @ORM\OneToMany(targetEntity="Intracto\GiftExchangeBundle\Domain\Pool\Participant\Entity", mappedBy="pool")
     */
    private $participants;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $eventDate;

    /**
     * @ORM\Column(type="decimal")
     */
    private $maxExpense;

    /**
     * @ORM\Column(type="boolean")
     */
    private $exposed;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $reminderSentAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->participants = new ArrayCollection();
        $this->exchanges = new ArrayCollection();
        $this->locale = 'en';
        $this->exposed = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
    public function getOwner()
    {
        return $this->owner;
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

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * @return string
     */
    public function getMaxExpense()
    {
        return $this->maxExpense;
    }

    /**
     * @return boolean
     */
    public function getExposed()
    {
        return $this->exposed;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getReminderSentAt()
    {
        return $this->reminderSentAt;
    }

    /**
     * @param Participant $owner
     * @param string $url
     * @param string $message
     * @param \DateTime $eventDate
     * @param double $maxExpense
     * @return Entity
     */
    static public function create(Participant $owner, $url, $message, $eventDate, $maxExpense)
    {
        $pool = new self();
        $pool->owner = $owner;
        $pool->url = $url;
        $pool->message = $message;
        $pool->eventDate = $eventDate;
        $pool->maxExpense = $maxExpense;

        $pool->validate();

        return $pool;
    }

    public function validate()
    {
        // Validate this object
        // Throw exception if not valid
    }
}