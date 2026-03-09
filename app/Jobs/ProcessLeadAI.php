<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\LeadAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessLeadAI implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $leadId
    ) {
    }

    public function handle(LeadAIService $leadAIService): void
    {
        $lead = Lead::query()->find($this->leadId);
        if (! $lead) {
            return;
        }

        $analysis = $leadAIService->analyzeLead($lead);

        $meta = $lead->meta ?? [];
        $meta['ai'] = [
            'extracted' => $analysis['extracted'] ?? [],
            'reasoning' => $analysis['reasoning'] ?? [],
            'provider' => $analysis['provider'] ?? 'heuristic',
            'fallback_used' => $analysis['fallback_used'] ?? true,
            'processed_at' => now()->toIso8601String(),
        ];

        $lead->update([
            'ai_score' => (int) ($analysis['ai_score'] ?? 0),
            'classification' => (string) ($analysis['classification'] ?? 'COLD'),
            'meta' => $meta,
        ]);
    }
}
