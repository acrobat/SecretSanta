<?php

namespace App\Query;

class Season
{
    private $start;

    private $end;

    /**
     * @param null $year
     */
    public function __construct($year = null)
    {
        $this->start = \DateTime::createFromFormat('Y-m-d', '2006-04-01');
        $this->end = new \DateTime();

        if ($year) {
            $this->start = \DateTime::createFromFormat('Y-m-d', $year.'-04-01');
            $this->end = \DateTime::createFromFormat('Y-m-d', $year + 1 .'-04-01');
        }
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }
}
