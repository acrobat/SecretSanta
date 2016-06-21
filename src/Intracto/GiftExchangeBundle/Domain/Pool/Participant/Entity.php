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

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $ipv4;

    /**
     * @ORM\Column(type="string", length=48)
     */
    private $ipv6;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $participatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $participationReminderSentAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $statusChangeReminderSentAt;

    private function __construct()
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
    public function getIpv4()
    {
        return $this->ipv4;
    }

    /**
     * @return string
     */
    public function getIpv6()
    {
        return $this->ipv6;
    }

    /**
     * @return \DateTime
     */
    public function getParticipatedAt()
    {
        return $this->participatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getParticipationReminderSentAt()
    {
        return $this->participationReminderSentAt;
    }

    /**
     * @return \DateTime
     */
    public function getStatusChangeReminderSentAt()
    {
        return $this->statusChangeReminderSentAt;
    }

    /**
     * @param Pool $pool
     * @param string $name
     * @param string $email
     * @param string $url
     * @param string $ipv4
     * @param string $ipv6
     * @return Entity
     * @throws \Exception
     */
    static public function create(Pool $pool, $name, $email, $url, $ipv4, $ipv6)
    {
        if ($name == '') {
            throw new \Exception('Name must be set.');
        }
        if ($email == '') {
            throw new \Exception('Email must be set.');
        }
        if ($url == '') {
            throw new \Exception('Url must be set.');
        }

        $participant = new self();
        $participant->pool = $pool;
        $participant->name = $name;
        $participant->email = $email;
        $participant->url = $url;
        $participant->ipv4 = $ipv4;
        $participant->ipv6 = $ipv6;
        $participant->wishList = WishList::create();

        return $participant;
    }
}