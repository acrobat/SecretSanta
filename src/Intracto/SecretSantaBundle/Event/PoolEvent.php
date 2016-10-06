<?php

namespace Intracto\SecretSantaBundle\Event;

use Intracto\Domain\Pool\Model\Pool;
use Symfony\Component\EventDispatcher\Event;

class PoolEvent extends Event
{
    private $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    public function getPool()
    {
        return $this->pool;
    }
}
