<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $leadCountsByStatus = Lead::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $dealCountsByStage = Deal::query()
            ->selectRaw('stage, COUNT(*) as count')
            ->groupBy('stage')
            ->pluck('count', 'stage');

        return view('admin.dashboard.index', [
            'metrics' => [
                'total_leads' => Lead::query()->count(),
                'hot_leads' => Lead::query()->where('classification', 'HOT')->count(),
                'total_deals' => Deal::query()->count(),
                'won_deals' => Deal::query()->where('stage', 'WON')->count(),
                'total_quotes' => Quote::query()->count(),
                'sent_quotes' => Quote::query()->where('status', 'SENT')->count(),
                'users' => User::query()->count(),
            ],
            'leadCountsByStatus' => $leadCountsByStatus,
            'dealCountsByStage' => $dealCountsByStage,
            'recentLeads' => Lead::query()->latest('id')->limit(8)->get(),
            'recentDeals' => Deal::query()->with('lead')->latest('id')->limit(8)->get(),
            'recentQuotes' => Quote::query()->with('deal.lead')->latest('id')->limit(8)->get(),
        ]);
    }
}
