<?php

namespace App\Entity;

class BlacklistEmail
{
    /** @var int */
    private $id;

    /** @var string */
    private $email;

    /** @var string */
    private $ipv4;

    /** @var string */
    private $ipv6;

    /** @var \DateTime */
    private $date;

    /**
     * BlacklistEmail constructor.
     *
     * @param string    $email
     * @param string    $ip
     * @param \DateTime $date
     */
    public function __construct($email, $ip, \DateTime $date)
    {
        $this->setIp($ip);
        $this->setEmail($email);
        $this->setDate($date);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    private function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        if ($this->getIpv4() !== null) {
            return $this->getIpv4();
        }

        return $this->getIpv6();
    }

    /**
     * @param string $ip
     */
    private function setIp($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->setIpv4($ip);
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->setIpv6($ip);
        }
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    private function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getIpv4()
    {
        return $this->ipv4;
    }

    /**
     * @param string $ipv4
     */
    private function setIpv4($ipv4)
    {
        $this->ipv4 = $ipv4;
    }

    /**
     * @return string
     */
    public function getIpv6()
    {
        return $this->ipv6;
    }

    /**
     * @param string $ipv6
     */
    private function setIpv6($ipv6)
    {
        $this->ipv6 = $ipv6;
    }
}
