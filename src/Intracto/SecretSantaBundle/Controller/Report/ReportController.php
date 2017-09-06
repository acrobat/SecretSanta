<?php
declare(strict_types=1);

namespace Intracto\SecretSantaBundle\Controller\Report;

use Intracto\SecretSantaBundle\Query\Season;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\VarDumper\VarDumper;

class ReportController extends Controller
{
    /**
     * @Route("/report/{year}", defaults={"year" = "all"}, name="report.report")
     * @Template("IntractoSecretSantaBundle:Report:index.html.twig")
     */
    public function indexAction(Season $season)
    {
        $reportQuery = $this->get('intracto_secret_santa.query.report');
        $partyReportQuery = $this->get('intracto_secret_santa.query.party_report');
        $participantReportQuery = $this->get('intracto_secret_santa.query.participant_report');
        $wishlistReportQuery = $this->get('intracto_secret_santa.query.wishlist_report');

        return [
            'years' => $reportQuery->getSelectableYears(),
            'current_year' => $season->getStart()->format('Y'),
            'parties' => $partyReportQuery->countParties($season),
            'participants' => $participantReportQuery->countParticipants($season),
            'confirmed_participants' => $participantReportQuery->countConfirmedParticipants($season),
            'distinct_participants' => $participantReportQuery->countDistinctParticipants($season),
            'participant_average' => $participantReportQuery->calculateAverageParticipantsPerParty($season),
            'wishlist_average' => $wishlistReportQuery->calculateCompletedWishlists($season),
            'total_parties' => $partyReportQuery->countAllPartiesUntilDate($season->getEnd()),
            'total_participants' => $participantReportQuery->countAllParticipantsUntilDate($season->getEnd()),
            'total_confirmed_participants' => $participantReportQuery->countConfirmedParticipantsUntilDate($season->getEnd()),
            'total_participant_average' => $participantReportQuery->calculateAverageParticipantsPerPartyUntilDate($season->getEnd()),
            'total_wishlist_average' => $wishlistReportQuery->calculateCompletedWishlistsUntilDate($season->getEnd()),
            'total_distinct_participants' => $participantReportQuery->countDistinctParticipantsUntilDate($season->getEnd()),
        ];
    }

    /**
     * @Route("/report/season-party-data/{year}", name="report_season_party_data")
     * @Method("GET")
     */
    public function seasonPartyData(Season $season)
    {
        $data = $this->get('intracto_secret_santa.service.report')->getSeasonPartyData($season);

        return $this->json($data);
    }

    /**
     * @Route("/report/season-participant-data/{year}", name="report_season_participant_data")
     * @Method("GET")
     */
    public function seasonParticipantData(Season $season)
    {
        $data = $this->get('intracto_secret_santa.service.report')->getSeasonParticipantData($season);

        return $this->json($data);
    }

    /**
     * @Route("/report/total-season-party-data/{year}", name="report_total_season_party_data")
     * @Method("GET")
     */
    public function totalSeasonPartyData(Season $season)
    {
        $data = $this->get('intracto_secret_santa.service.report')->getTotalSeasonPartyData($season);

        return $this->json($data);
    }

    /**
     * @Route("/report/total-season-participant-data/{year}", name="report_total_season_participant_data")
     * @Method("GET")
     */
    public function totalSeasonParticipantData(Season $season)
    {
        $data = $this->get('intracto_secret_santa.service.report')->getTotalSeasonParticipantData($season);

        return $this->json($data);
    }
}
