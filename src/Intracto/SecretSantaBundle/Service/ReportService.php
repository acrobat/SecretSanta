<?php
declare(strict_types=1);

namespace Intracto\SecretSantaBundle\Service;

use Doctrine\DBAL\Driver\Connection;
use Intracto\SecretSantaBundle\Query\Season;

class ReportService
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function getSeasonPartyData(Season $season)
    {
        $q = '
SELECT count(p.id) AS accumulatedPartyCountByMonth, MONTHNAME(p.sent_date) AS month 
FROM party p 
WHERE p.sent_date >= :firstDay
AND p.sent_date < :lastDay
GROUP BY month(p.sent_date)
ORDER BY month(p.sent_date) < 4, month(p.sent_date)';

        return $this->conn->executeQuery($q, [
            'firstDay' => $season->getStart()->format('Y-m-d H:i:s'),
            'lastDay' => $season->getEnd()->format('Y-m-d H:i:s')
        ])->fetchAll();
    }

    public function getSeasonParticipantData(Season $season)
    {
        $q = 'SELECT count(p.id) AS accumulatedParticipantCountByMonth, MONTHNAME(p.sent_date) AS month
        FROM party p 
        JOIN participant e ON p.id = e.party_id
        WHERE p.sent_date >= :firstDay
        AND p.sent_date < :lastDay
        GROUP BY month(p.sent_date)
        ORDER BY month(p.sent_date) < 4, month(p.sent_date)';

        return $this->conn->executeQuery($q, [
            'firstDay' => $season->getStart()->format('Y-m-d H:i:s'),
            'lastDay' => $season->getEnd()->format('Y-m-d H:i:s')
        ])->fetchAll();
    }

    public function getTotalSeasonPartyData(Season $season)
    {
        $q = '
        SELECT count(p.id) AS totalPartyCount, CONCAT(MONTHNAME(p.sent_date), \' \', YEAR(p.sent_date)) AS month
        FROM party p
        WHERE p.sent_date < :lastDay
        GROUP BY year(p.sent_date), month(p.sent_date)
        ';

        $result = $this->conn->executeQuery(
            $q,
            ['lastDay' => $season->getEnd()->format('Y-m-d H:i:s')]
        )->fetchAll();

        $accumulatedPartyCounter = 0;
        foreach ($result as &$partyCount) {
            $accumulatedPartyCounter += $partyCount['totalPartyCount'];
            $partyCount['totalPartyCount'] = $accumulatedPartyCounter;
        }

        return $result;
    }

    public function getTotalSeasonParticipantData(Season $season)
    {
        $q = 'SELECT count(p.id) AS totalParticipantCount, CONCAT(MONTHNAME(p.sent_date), \' \', YEAR(p.sent_date)) AS month
        FROM party p 
        JOIN participant e ON p.id = e.party_id
        WHERE p.sent_date < :lastDay
        GROUP BY year(p.sent_date), month(p.sent_date)';

        $result = $this->conn->executeQuery(
            $q,
            ['lastDay' => $season->getEnd()->format('Y-m-d H:i:s')]
        )->fetchAll();

        $accumulatedParticipantCounter = 0;

        foreach ($result as &$participantCount) {
            $accumulatedParticipantCounter += $participantCount['totalParticipantCount'];
            $participantCount['totalParticipantCount'] = $accumulatedParticipantCounter;
        }

        return $result;
    }
}
