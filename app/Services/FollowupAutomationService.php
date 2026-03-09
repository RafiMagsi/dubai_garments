<?php

namespace App\Services;

use App\Mail\AdminCommunicationMail;
use App\Models\Communication;
use App\Models\Followup;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;
use Throwable;

class FollowupAutomationService
{
    public function scheduleQuoteSentFollowups(Quote $quote): void
    {
        $quote->loadMissing('deal.lead');

        if (! $quote->deal_id) {
            return;
        }

        $templates = [
            [
                'step' => 'quote_followup_day_2',
                'offset_days' => 2,
                'subject' => 'Checking in on your quote '.$quote->quote_number,
                'message' => "Hello %s,\n\nJust checking if you had time to review quote {$quote->quote_number}. We can adjust pricing or quantities if required.\n\nRegards,\nDubai Garments Sales Team",
            ],
            [
                'step' => 'quote_followup_day_5',
                'offset_days' => 5,
                'subject' => 'Reminder: quote '.$quote->quote_number,
                'message' => "Hello %s,\n\nThis is a friendly reminder regarding quote {$quote->quote_number}. Let us know if you want to proceed or need changes.\n\nRegards,\nDubai Garments Sales Team",
            ],
            [
                'step' => 'quote_followup_day_10',
                'offset_days' => 10,
                'subject' => 'Final follow-up for quote '.$quote->quote_number,
                'message' => "Hello %s,\n\nFinal follow-up for quote {$quote->quote_number}. Please reply if you want us to keep this quote active.\n\nRegards,\nDubai Garments Sales Team",
            ],
        ];

        $recipientName = $quote->deal?->lead?->customer_name ?: 'Customer';

        foreach ($templates as $template) {
            Followup::query()->firstOrCreate(
                [
                    'quote_id' => $quote->id,
                    'step' => $template['step'],
                ],
                [
                    'deal_id' => $quote->deal_id,
                    'next_run' => now()->addDays($template['offset_days']),
                    'status' => 'pending',
                    'subject' => $template['subject'],
                    'message' => sprintf($template['message'], $recipientName),
                    'meta' => [
                        'source' => 'quote_sent_automation',
                        'offset_days' => $template['offset_days'],
                    ],
                ]
            );
        }
    }

    /**
     * @return array{processed:int,sent:int,failed:int,skipped:int}
     */
    public function runDueFollowups(int $limit = 100): array
    {
        $dueFollowups = Followup::query()
            ->with(['quote.deal.lead', 'deal.lead'])
            ->where('status', 'pending')
            ->whereNotNull('next_run')
            ->where('next_run', '<=', now())
            ->orderBy('next_run')
            ->limit($limit)
            ->get();

        $stats = [
            'processed' => $dueFollowups->count(),
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($dueFollowups as $followup) {
            $lead = $followup->quote?->deal?->lead ?: $followup->deal?->lead;
            $recipientEmail = $lead?->email;

            if (! $recipientEmail || ! $followup->subject || ! $followup->message) {
                $followup->update([
                    'status' => 'skipped',
                    'error_message' => 'Missing recipient email or follow-up content.',
                ]);
                $stats['skipped']++;
                continue;
            }

            try {
                Mail::to($recipientEmail)->send(new AdminCommunicationMail(
                    subjectLine: $followup->subject,
                    messageBody: $followup->message,
                    lead: $lead,
                    deal: $followup->deal,
                    quote: $followup->quote,
                ));

                $followup->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                ]);

                Communication::query()->create([
                    'lead_id' => $lead?->id,
                    'deal_id' => $followup->deal_id,
                    'quote_id' => $followup->quote_id,
                    'direction' => 'outgoing',
                    'recipient_email' => $recipientEmail,
                    'subject' => $followup->subject,
                    'message' => $followup->message,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $stats['sent']++;
            } catch (Throwable $exception) {
                $followup->update([
                    'status' => 'failed',
                    'error_message' => $exception->getMessage(),
                ]);

                Communication::query()->create([
                    'lead_id' => $lead?->id,
                    'deal_id' => $followup->deal_id,
                    'quote_id' => $followup->quote_id,
                    'direction' => 'outgoing',
                    'recipient_email' => $recipientEmail,
                    'subject' => $followup->subject,
                    'message' => $followup->message,
                    'status' => 'failed',
                    'error_message' => $exception->getMessage(),
                ]);

                $stats['failed']++;
            }
        }

        return $stats;
    }
}
