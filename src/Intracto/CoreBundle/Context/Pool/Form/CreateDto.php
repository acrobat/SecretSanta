<?php
namespace Intracto\CoreBundle\Context\Pool\Form;

/**
 * Class CreateDto.
 */
class CreateDto
{
    /**
     * @var string message
     */
    private $message = '';

    /**
     * @var \DateTime eventdate
     */
    private $eventdate;

    /**
     * @var float amount
     */
    private $amount = 0.00;

    /**
     * @var string location
     */
    private $location = '';

    /**
     * @var array entries
     */
    private $entries = [];

    public function __construct()
    {
        $this->eventdate = new \DateTime();
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message ?: '';
    }

    /**
     * @return \DateTime
     */
    public function getEventdate()
    {
        return $this->eventdate;
    }

    /**
     * @param \DateTime $eventdate
     */
    public function setEventdate($eventdate)
    {
        $this->eventdate = $eventdate ?: new \DateTime();
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount ?: 0.00;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location ?: '';
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param array $entries
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

}
