<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowupManagementController extends Controller
{
    public function index(Request $request): View
    {
        $status = strtolower(trim((string) $request->query('status', '')));
        $quoteNumber = trim((string) $request->query('quote', ''));

        $followups = Followup::query()
            ->with(['deal.lead', 'quote'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($quoteNumber !== '', function ($query) use ($quoteNumber) {
                $query->whereHas('quote', fn ($quoteQuery) => $quoteQuery->where('quote_number', 'like', "%{$quoteNumber}%"));
            })
            ->orderBy('status')
            ->orderBy('next_run')
            ->paginate(20)
            ->withQueryString();

        return view('admin.followups.index', [
            'followups' => $followups,
            'status' => $status,
            'quoteNumber' => $quoteNumber,
            'statuses' => ['pending', 'sent', 'failed', 'skipped'],
        ]);
    }
}
