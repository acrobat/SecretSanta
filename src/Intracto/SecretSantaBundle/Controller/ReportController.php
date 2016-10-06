<?php

namespace Intracto\SecretSantaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReportController.
 */
class ReportController extends Controller
{
    /**
     * @param int $year
     *
     * @return Response
     */
    public function reportAction($year)
    {
        $analyticsQuery = $this->get('analytics.query.google_analytics_query');
        $report = $this->get('analytics.query.report_query');
        $comparison = $this->get('analytics.query.season_comparison_report_query');
        $featuredYears = $this->get('analytics.query.featured_years_query')->getFeaturedYears();

        if ($reportQueryResult = $this->get('cache')->fetch('data'.$year)) {
            $cache = unserialize($reportQueryResult);

            $data = [
                'current_year' => $year,
                'data_pool' => $cache['data_pool'],
                'featured_years' => $cache['featured_years'],
                'google_data_pool' => $cache['google_data_pool'],
            ];

            if (isset($cache['difference_data_pool'])) {
                $data['difference_data_pool'] = $cache['difference_data_pool'];
            }

            return $this->render('IntractoSecretSantaBundle:Report:report.html.twig', $data);
        }

        try {
            if ($year != 'all') {
                $dataPool = $report->getPoolReport($year);
            } else {
                $dataPool = $report->getPoolReport();
            }
        } catch (\Exception $e) {
            $dataPool = [];
        }

        try {
            if ($year != 'all') {
                $googleDataPool = $analyticsQuery->getAnalyticsReport($year);
            } else {
                $googleDataPool = $analyticsQuery->getAnalyticsReport();
            }
        } catch (\Exception $e) {
            $googleDataPool = [];
        }

        try {
            if ($year != 'all') {
                $differenceDataPool = $comparison->getComparison($year);
            }
        } catch (\Exception $e) {
            $differenceDataPool = [];
        }

        $data = [
            'current_year' => $year,
            'data_pool' => $dataPool,
            'featured_years' => $featuredYears,
            'google_data_pool' => $googleDataPool,
        ];

        if (isset($differenceDataPool)) {
            $data['difference_data_pool'] = $differenceDataPool;
        }

        end($featuredYears['featured_years']);
        $lastKey = key($featuredYears['featured_years']);

        if ($year == 'all' || $year == $featuredYears['featured_years'][$lastKey]) {
            $this->get('cache')->save('data'.$year, serialize($data), 24 * 60 * 60);

            return $this->render('IntractoSecretSantaBundle:Report:report.html.twig', $data);
        }

        $this->get('cache')->save('data'.$year, serialize($data));

        return $this->render('IntractoSecretSantaBundle:Report:report.html.twig', $data);
    }
}
