<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Support\CatalogData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function index(Request $request, CatalogData $catalogData): View
    {
        return view('storefront.portal.index', [
            'categories' => $catalogData->categories(),
            'prefillEmail' => (string) $request->query('email', ''),
            'prefillCode' => (string) $request->query('code', ''),
        ]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'tracking_code' => ['required', 'string', 'max:32'],
        ]);

        $trackingCode = strtoupper(trim($validated['tracking_code']));

        $lead = Lead::query()
            ->where('email', $validated['email'])
            ->where('tracking_code', $trackingCode)
            ->first();

        if (! $lead) {
            return back()
                ->withInput()
                ->withErrors(['tracking_code' => 'We could not find a request with this email and tracking code.']);
        }

        $request->session()->put('customer_portal.active_tracking_code', $trackingCode);
        $request->session()->put('customer_portal.active_email', $validated['email']);

        return redirect()->route('portal.requests.show', ['trackingCode' => $trackingCode]);
    }

    public function show(Request $request, string $trackingCode, CatalogData $catalogData): View|RedirectResponse
    {
        $sessionCode = (string) $request->session()->get('customer_portal.active_tracking_code', '');
        $sessionEmail = (string) $request->session()->get('customer_portal.active_email', '');

        if ($sessionCode !== $trackingCode || $sessionEmail === '') {
            return redirect()
                ->route('portal.index')
                ->withErrors(['tracking_code' => 'Please verify your request using email and tracking code.']);
        }

        $lead = Lead::query()
            ->where('tracking_code', $trackingCode)
            ->where('email', $sessionEmail)
            ->first();

        if (! $lead) {
            $request->session()->forget(['customer_portal.active_tracking_code', 'customer_portal.active_email']);

            return redirect()
                ->route('portal.index')
                ->withErrors(['tracking_code' => 'Request not found. Please verify your details again.']);
        }

        return view('storefront.portal.show', [
            'categories' => $catalogData->categories(),
            'lead' => $lead,
            'statusSteps' => $this->statusSteps((string) $lead->status),
        ]);
    }

    private function statusSteps(string $currentStatus): array
    {
        $steps = ['NEW', 'QUALIFIED', 'QUOTED', 'NEGOTIATION', 'WON'];
        $currentIndex = array_search($currentStatus, $steps, true);
        $currentIndex = $currentIndex === false ? 0 : $currentIndex;

        return array_map(function (string $step, int $index) use ($currentIndex): array {
            return [
                'name' => $step,
                'state' => $index < $currentIndex ? 'done' : ($index === $currentIndex ? 'current' : 'upcoming'),
            ];
        }, $steps, array_keys($steps));
    }
}
