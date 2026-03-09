<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DealManagementController extends Controller
{
    private const STAGES = ['NEW', 'QUALIFIED', 'QUOTED', 'NEGOTIATION', 'WON', 'LOST'];
    private const PRIORITIES = ['low', 'medium', 'high'];

    public function index(Request $request): View
    {
        $stage = strtoupper(trim((string) $request->query('stage', '')));
        $search = trim((string) $request->query('search', ''));

        $dealsQuery = Deal::query()
            ->with(['lead', 'assignedUser'])
            ->when(in_array($stage, self::STAGES, true), fn ($query) => $query->where('stage', $stage))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('lead', function ($leadQuery) use ($search) {
                        $leadQuery->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('company', 'like', "%{$search}%")
                            ->orWhere('tracking_code', 'like', "%{$search}%")
                            ->orWhere('product_type', 'like', "%{$search}%");
                    })->orWhere('notes', 'like', "%{$search}%");
                });
            });

        $deals = (clone $dealsQuery)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $pipeline = [];
        foreach (self::STAGES as $pipelineStage) {
            $pipeline[$pipelineStage] = (clone $dealsQuery)
                ->where('stage', $pipelineStage)
                ->orderByDesc('updated_at')
                ->limit(6)
                ->get();
        }

        return view('admin.deals.index', [
            'deals' => $deals,
            'pipeline' => $pipeline,
            'stages' => self::STAGES,
            'stage' => $stage,
            'search' => $search,
        ]);
    }

    public function show(int $deal): View
    {
        $dealModel = Deal::query()->with(['lead', 'assignedUser', 'quotes'])->findOrFail($deal);

        return view('admin.deals.show', [
            'deal' => $dealModel,
            'stages' => self::STAGES,
            'priorities' => self::PRIORITIES,
            'assignableUsers' => User::query()->whereIn('role', ['admin', 'sales'])->orderBy('name')->get(),
            'communications' => $dealModel->communications()->latest('id')->limit(8)->get(),
        ]);
    }

    public function createFromLead(Request $request, int $lead): RedirectResponse
    {
        $leadModel = Lead::query()->findOrFail($lead);

        if ($leadModel->deal()->exists()) {
            return redirect()
                ->route('admin.deals.show', ['deal' => $leadModel->deal->id])
                ->with('status', 'Deal already exists for this lead.');
        }

        $validated = $request->validate([
            'priority' => ['nullable', Rule::in(self::PRIORITIES)],
            'value_estimate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $deal = Deal::query()->create([
            'lead_id' => $leadModel->id,
            'stage' => 'NEW',
            'priority' => $validated['priority'] ?? 'medium',
            'value_estimate' => $validated['value_estimate'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $leadModel->update([
            'status' => 'NEW',
        ]);

        return redirect()
            ->route('admin.deals.show', ['deal' => $deal->id])
            ->with('status', 'Deal created successfully from lead.');
    }

    public function update(Request $request, int $deal): RedirectResponse
    {
        $dealModel = Deal::query()->findOrFail($deal);

        $validated = $request->validate([
            'stage' => ['required', Rule::in(self::STAGES)],
            'priority' => ['required', Rule::in(self::PRIORITIES)],
            'value_estimate' => ['nullable', 'numeric', 'min:0'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $dealModel->update($validated);

        if ($dealModel->lead) {
            $dealModel->lead->update([
                'status' => $validated['stage'],
            ]);
        }

        return redirect()
            ->route('admin.deals.show', ['deal' => $dealModel->id])
            ->with('status', 'Deal updated successfully.');
    }
}
