<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminCommunicationMail;
use App\Models\Communication;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CommunicationController extends Controller
{
    public function sendForLead(Request $request, int $lead): RedirectResponse
    {
        $leadModel = Lead::query()->with('deal')->findOrFail($lead);

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:8000'],
        ]);

        return $this->sendCommunication(
            recipientEmail: $validated['recipient_email'],
            subject: $validated['subject'],
            message: $validated['message'],
            lead: $leadModel,
            deal: $leadModel->deal,
            quote: null,
            redirectRoute: 'admin.leads.show',
            redirectParams: ['lead' => $leadModel->id],
        );
    }

    public function sendForDeal(Request $request, int $deal): RedirectResponse
    {
        $dealModel = Deal::query()->with('lead')->findOrFail($deal);

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:8000'],
        ]);

        return $this->sendCommunication(
            recipientEmail: $validated['recipient_email'],
            subject: $validated['subject'],
            message: $validated['message'],
            lead: $dealModel->lead,
            deal: $dealModel,
            quote: null,
            redirectRoute: 'admin.deals.show',
            redirectParams: ['deal' => $dealModel->id],
        );
    }

    public function sendForQuote(Request $request, int $quote): RedirectResponse
    {
        $quoteModel = Quote::query()->with('deal.lead')->findOrFail($quote);

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:8000'],
        ]);

        return $this->sendCommunication(
            recipientEmail: $validated['recipient_email'],
            subject: $validated['subject'],
            message: $validated['message'],
            lead: $quoteModel->deal?->lead,
            deal: $quoteModel->deal,
            quote: $quoteModel,
            redirectRoute: 'admin.quotes.show',
            redirectParams: ['quote' => $quoteModel->id],
        );
    }

    private function sendCommunication(
        string $recipientEmail,
        string $subject,
        string $message,
        ?Lead $lead,
        ?Deal $deal,
        ?Quote $quote,
        string $redirectRoute,
        array $redirectParams,
    ): RedirectResponse {
        try {
            Mail::to($recipientEmail)->send(new AdminCommunicationMail(
                subjectLine: $subject,
                messageBody: $message,
                lead: $lead,
                deal: $deal,
                quote: $quote,
            ));

            Communication::query()->create([
                'lead_id' => $lead?->id,
                'deal_id' => $deal?->id,
                'quote_id' => $quote?->id,
                'direction' => 'outgoing',
                'recipient_email' => $recipientEmail,
                'subject' => $subject,
                'message' => $message,
                'status' => 'sent',
                'sent_by_user_id' => Auth::id(),
                'sent_at' => now(),
            ]);

            return redirect()
                ->route($redirectRoute, $redirectParams)
                ->with('status', 'Email sent successfully.');
        } catch (Throwable $exception) {
            Communication::query()->create([
                'lead_id' => $lead?->id,
                'deal_id' => $deal?->id,
                'quote_id' => $quote?->id,
                'direction' => 'outgoing',
                'recipient_email' => $recipientEmail,
                'subject' => $subject,
                'message' => $message,
                'status' => 'failed',
                'sent_by_user_id' => Auth::id(),
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route($redirectRoute, $redirectParams)
                ->withErrors([
                    'email' => 'Email sending failed. Check SMTP settings and logs.',
                ]);
        }
    }
}
