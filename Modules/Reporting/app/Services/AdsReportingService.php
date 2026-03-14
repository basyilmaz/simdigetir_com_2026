<?php

namespace Modules\Reporting\Services;

use Modules\AdsCore\Models\AdDailyMetric;
use Illuminate\Support\Facades\DB;

class AdsReportingService
{
    public function campaignPerformanceSummary(string $fromDate, string $toDate): array
    {
        $rows = DB::table('ad_campaigns')
            ->leftJoin('ad_conversions', 'ad_campaigns.id', '=', 'ad_conversions.ad_campaign_id')
            ->selectRaw('ad_campaigns.platform as platform')
            ->selectRaw('count(distinct ad_campaigns.id) as campaigns')
            ->selectRaw("sum(case when ad_conversions.status = 'sent' then 1 else 0 end) as sent_conversions")
            ->whereDate('ad_campaigns.created_at', '>=', $fromDate)
            ->whereDate('ad_campaigns.created_at', '<=', $toDate)
            ->groupBy('ad_campaigns.platform')
            ->get();

        return $rows->map(fn ($row) => [
            'platform' => $row->platform,
            'campaigns' => (int) $row->campaigns,
            'sent_conversions' => (int) $row->sent_conversions,
        ])->all();
    }

    /**
     * @param  array{from:string,to:string,platform?:string,campaign_id?:int}  $filters
     * @return array{totals:array<string,float|int>,rows:array<int,array<string,mixed>>}
     */
    public function performanceDashboard(array $filters): array
    {
        $query = AdDailyMetric::query()
            ->whereDate('metric_date', '>=', $filters['from'])
            ->whereDate('metric_date', '<=', $filters['to']);

        if (! empty($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }
        if (! empty($filters['campaign_id'])) {
            $query->where('ad_campaign_id', $filters['campaign_id']);
        }

        $rows = $query
            ->orderBy('metric_date')
            ->get([
                'metric_date',
                'platform',
                'ad_campaign_id',
                'campaign_name',
                'spend',
                'leads',
                'revenue',
                'roas',
            ]);

        $spend = (float) $rows->sum('spend');
        $revenue = (float) $rows->sum('revenue');
        $leads = (int) $rows->sum('leads');
        $roas = $spend > 0 ? round($revenue / $spend, 4) : 0.0;

        return [
            'totals' => [
                'spend' => round($spend, 2),
                'revenue' => round($revenue, 2),
                'leads' => $leads,
                'roas' => $roas,
            ],
            'rows' => $rows->map(fn (AdDailyMetric $metric) => [
                'date' => optional($metric->metric_date)->toDateString(),
                'platform' => $metric->platform,
                'campaign_id' => $metric->ad_campaign_id,
                'campaign_name' => $metric->campaign_name,
                'spend' => (float) $metric->spend,
                'revenue' => (float) $metric->revenue,
                'leads' => (int) $metric->leads,
                'roas' => (float) $metric->roas,
            ])->all(),
        ];
    }

    public function aggregateDailyMetrics(string $metricDate): int
    {
        $date = date('Y-m-d', strtotime($metricDate));

        $campaigns = DB::table('ad_campaigns')
            ->select('id', 'platform', 'name', 'daily_budget')
            ->get();

        $updated = 0;
        foreach ($campaigns as $campaign) {
            $stats = DB::table('ad_conversions')
                ->where('ad_campaign_id', $campaign->id)
                ->whereDate('created_at', $date)
                ->selectRaw("sum(case when status = 'sent' then 1 else 0 end) as leads")
                ->selectRaw("coalesce(sum(case when status = 'sent' then value else 0 end), 0) as revenue")
                ->first();

            $spend = (float) ($campaign->daily_budget ?? 0);
            $revenue = (float) ($stats->revenue ?? 0);
            $leads = (int) ($stats->leads ?? 0);
            $roas = $spend > 0 ? round($revenue / $spend, 4) : 0;

            AdDailyMetric::query()->updateOrCreate(
                [
                    'metric_date' => $date,
                    'platform' => (string) $campaign->platform,
                    'ad_campaign_id' => (int) $campaign->id,
                ],
                [
                    'campaign_name' => (string) $campaign->name,
                    'spend' => $spend,
                    'leads' => $leads,
                    'revenue' => $revenue,
                    'roas' => $roas,
                ]
            );

            $updated++;
        }

        return $updated;
    }
}
