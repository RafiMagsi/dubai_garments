<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadManagementController extends Controller
{
    private const STATUSES = ['NEW', 'QUALIFIED', 'QUOTED', 'NEGOTIATION', 'WON', 'LOST'];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = strtoupper(trim((string) $request->query('status', '')));

        $leads = Lead::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('tracking_code', 'like', "%{$search}%")
                        ->orWhere('product_type', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, self::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'search' => $search,
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(int $leadId): View
    {
        $lead = Lead::query()->with('deal')->findOrFail($leadId);

        return view('admin.leads.show', [
            'lead' => $lead,
            'statuses' => self::STATUSES,
            'communications' => $lead->communications()->latest('id')->limit(8)->get(),
        ]);
    }

    public function updateStatus(Request $request, int $leadId): RedirectResponse
    {
        $lead = Lead::query()->findOrFail($leadId);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
        ]);

        $lead->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.leads.show', ['lead' => $lead->id])
            ->with('status', 'Lead status updated successfully.');
    }
}
