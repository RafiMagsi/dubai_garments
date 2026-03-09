<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LeadAIService
{
    public function analyzeLead(Lead $lead): array
    {
        $heuristic = $this->heuristicAnalysis($lead);
        $openAi = $this->openAiAnalysis($lead, $heuristic);

        if ($openAi !== null) {
            return $openAi + [
                'provider' => 'openai',
                'fallback_used' => false,
            ];
        }

        return $heuristic + [
            'provider' => 'heuristic',
            'fallback_used' => true,
        ];
    }

    private function heuristicAnalysis(Lead $lead): array
    {
        $message = mb_strtolower((string) $lead->message);
        $quantity = (int) ($lead->quantity ?? 0);
        $score = 35;

        if ($quantity >= 1000) {
            $score += 30;
        } elseif ($quantity >= 500) {
            $score += 22;
        } elseif ($quantity >= 200) {
            $score += 14;
        } elseif ($quantity >= 100) {
            $score += 8;
        }

        if ($lead->company) {
            $score += 10;
        }

        if ($lead->required_delivery_date) {
            $daysUntilDelivery = now()->diffInDays($lead->required_delivery_date, false);
            if ($daysUntilDelivery <= 7) {
                $score += 12;
            } elseif ($daysUntilDelivery <= 21) {
                $score += 8;
            } elseif ($daysUntilDelivery <= 45) {
                $score += 4;
            }
        }

        $intentKeywords = ['urgent', 'asap', 'immediately', 'event', 'conference', 'campaign', 'uniform'];
        foreach ($intentKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                $score += 2;
            }
        }

        $score = max(0, min(100, $score));

        $classification = $score >= 75 ? 'HOT' : ($score >= 55 ? 'WARM' : 'COLD');

        $urgency = 'normal';
        if (str_contains($message, 'urgent') || str_contains($message, 'asap')) {
            $urgency = 'high';
        } elseif ($lead->required_delivery_date && now()->diffInDays($lead->required_delivery_date, false) <= 14) {
            $urgency = 'high';
        }

        return [
            'ai_score' => $score,
            'classification' => $classification,
            'extracted' => [
                'product' => $lead->product_type,
                'quantity' => $lead->quantity,
                'urgency' => $urgency,
                'complexity' => $quantity >= 500 ? 'high' : ($quantity >= 150 ? 'medium' : 'low'),
            ],
            'reasoning' => [
                'quantity' => $quantity,
                'has_company' => (bool) $lead->company,
                'has_delivery_date' => (bool) $lead->required_delivery_date,
                'message_length' => mb_strlen((string) $lead->message),
            ],
        ];
    }

    private function openAiAnalysis(Lead $lead, array $fallback): ?array
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.model', 'gpt-4.1-mini');

        if (! $apiKey) {
            Log::info('LeadAIService: OpenAI key missing, using heuristic fallback.', [
                'lead_id' => $lead->id,
            ]);
            return null;
        }

        $systemPrompt = 'Score this B2B garment lead and classify as HOT, WARM, or COLD.';

        $leadPayload = [
            'customer_name' => $lead->customer_name,
            'company' => $lead->company,
            'email' => $lead->email,
            'product_type' => $lead->product_type,
            'quantity' => $lead->quantity,
            'required_delivery_date' => optional($lead->required_delivery_date)->toDateString(),
            'message' => $lead->message,
        ];

        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'ai_score' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100],
                'classification' => ['type' => 'string', 'enum' => ['HOT', 'WARM', 'COLD']],
                'extracted' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'product' => ['type' => 'string'],
                        'quantity' => ['type' => 'integer'],
                        'urgency' => ['type' => 'string', 'enum' => ['high', 'normal', 'low']],
                        'complexity' => ['type' => 'string', 'enum' => ['high', 'medium', 'low']],
                    ],
                    'required' => ['product', 'quantity', 'urgency', 'complexity'],
                ],
                'reasoning' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'summary' => ['type' => 'string'],
                    ],
                    'required' => ['summary'],
                ],
            ],
            'required' => ['ai_score', 'classification', 'extracted', 'reasoning'],
        ];

        try {
            $requestPayload = [
                'model' => $model,
                // Keep structured lead payload as a first-class object in request body.
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [
                            ['type' => 'input_text', 'text' => $systemPrompt],
                        ],
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'input_text', 'text' => 'Lead data object follows.'],
                            ['type' => 'input_text', 'text' => json_encode($leadPayload, JSON_UNESCAPED_UNICODE)],
                        ],
                    ],
                ],
                'temperature' => 0.2,
                'max_output_tokens' => 220,
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'lead_analysis',
                        'schema' => $schema,
                        'strict' => true,
                    ],
                ],
            ];

            Log::info('LeadAIService: Calling OpenAI Responses API.', [
                'lead_id' => $lead->id,
                'model' => $model,
                'temperature' => 0.2,
                'max_output_tokens' => 220,
                'request_fields' => ['model', 'input', 'temperature', 'max_output_tokens', 'text.format'],
            ]);

            $response = Http::timeout(20)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/responses', $requestPayload);

            Log::info('LeadAIService: OpenAI raw response received.', [
                'lead_id' => $lead->id,
                'http_status' => $response->status(),
                'response_id' => $response->json('id'),
                'response_status' => $response->json('status'),
                'usage' => $response->json('usage'),
            ]);

            if (! $response->successful()) {
                Log::warning('LeadAIService: OpenAI request failed.', [
                    'lead_id' => $lead->id,
                    'http_status' => $response->status(),
                    'response_error' => $response->json('error'),
                ]);
                return null;
            }

            $payload = $response->json() ?: [];
            $jsonText = $this->extractTextFromResponsePayload($payload);
            if ($jsonText === '') {
                Log::warning('LeadAIService: OpenAI output_text empty.', [
                    'lead_id' => $lead->id,
                    'response_keys' => array_keys($payload),
                    'first_output_item' => $payload['output'][0] ?? null,
                ]);
                return null;
            }

            $decoded = json_decode($jsonText, true);
            if (! is_array($decoded)) {
                $jsonCandidate = $this->extractJsonObject($jsonText);
                if ($jsonCandidate !== null) {
                    $decoded = json_decode($jsonCandidate, true);
                }
            }

            if (! is_array($decoded)) {
                Log::warning('LeadAIService: OpenAI output_text is not valid JSON.', [
                    'lead_id' => $lead->id,
                    'output_text_preview' => mb_substr($jsonText, 0, 600),
                ]);
                return null;
            }

            $score = (int) ($decoded['ai_score'] ?? 0);
            $classification = strtoupper((string) ($decoded['classification'] ?? ''));
            if (! in_array($classification, ['HOT', 'WARM', 'COLD'], true)) {
                $classification = $fallback['classification'];
            }

            return [
                'ai_score' => max(0, min(100, $score)),
                'classification' => $classification,
                'extracted' => is_array($decoded['extracted'] ?? null) ? $decoded['extracted'] : $fallback['extracted'],
                'reasoning' => is_array($decoded['reasoning'] ?? null) ? $decoded['reasoning'] : $fallback['reasoning'],
            ];
        } catch (Throwable $e) {
            Log::error('LeadAIService: Exception while calling OpenAI.', [
                'lead_id' => $lead->id,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function extractTextFromResponsePayload(array $payload): string
    {
        $direct = (string) ($payload['output_text'] ?? '');
        if ($direct !== '') {
            return trim($direct);
        }

        $chunks = [];
        $output = $payload['output'] ?? [];
        if (! is_array($output)) {
            return '';
        }

        foreach ($output as $item) {
            if (! is_array($item)) {
                continue;
            }

            $content = $item['content'] ?? [];
            if (! is_array($content)) {
                continue;
            }

            foreach ($content as $part) {
                if (! is_array($part)) {
                    continue;
                }

                $rawText = $part['text'] ?? '';
                $text = '';
                if (is_string($rawText)) {
                    $text = $rawText;
                } elseif (is_array($rawText)) {
                    $text = (string) ($rawText['value'] ?? '');
                }

                // Handle refusal-style blocks as fallback textual output.
                if ($text === '' && isset($part['refusal']) && is_string($part['refusal'])) {
                    $text = $part['refusal'];
                }

                if ($text !== '') {
                    $chunks[] = $text;
                }
            }
        }

        return trim(implode("\n", $chunks));
    }

    private function extractJsonObject(string $text): ?string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        return substr($text, $start, $end - $start + 1);
    }
}
