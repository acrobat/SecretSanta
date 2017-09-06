<?php

namespace Intracto\SecretSantaBundle\Query;

use Doctrine\DBAL\Connection;

class ReportQuery
{
    /** @var Connection */
    private $conn;

    /**
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Return all years that can be selected, based on sent_data in the party table.
     *
     * @return int[]
     */
    public function getSelectableYears(): array
    {
        return array_column($this->conn->fetchAll('SELECT DISTINCT YEAR(created) as year FROM party'), 'year');
    }
}
