<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends Controller
{
    private const STATUSES = ['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'EXPIRED'];

    public function index(Request $request): View
    {
        $status = strtoupper(trim((string) $request->query('status', '')));
        $search = trim((string) $request->query('search', ''));

        $quotes = Quote::query()
            ->with(['deal.lead'])
            ->when(in_array($status, self::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where('quote_number', 'like', "%{$search}%")
                    ->orWhereHas('deal.lead', function ($leadQuery) use ($search) {
                        $leadQuery->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('company', 'like', "%{$search}%")
                            ->orWhere('tracking_code', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.quotes.index', [
            'quotes' => $quotes,
            'statuses' => self::STATUSES,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(int $quote): View
    {
        $quoteModel = Quote::query()->with(['deal.lead', 'deal.assignedUser'])->findOrFail($quote);

        return view('admin.quotes.show', [
            'quote' => $quoteModel,
            'statuses' => self::STATUSES,
            'itemsText' => $this->itemsToText((array) ($quoteModel->items_json ?? [])),
            'communications' => $quoteModel->communications()->latest('id')->limit(8)->get(),
        ]);
    }

    public function downloadPdf(int $quote): Response
    {
        $quoteModel = Quote::query()->with(['deal.lead', 'deal.assignedUser'])->findOrFail($quote);

        $pdf = Pdf::loadView('admin.quotes.pdf', [
            'quote' => $quoteModel,
        ]);

        $filename = ($quoteModel->quote_number ?: 'quote-'.$quoteModel->id).'.pdf';

        return $pdf->download($filename);
    }

    public function createFromDeal(Request $request, int $deal): RedirectResponse
    {
        $dealModel = Deal::query()->with('lead')->findOrFail($deal);
        $parsed = $this->parseItemsText((string) $request->input('items_text', ''));

        $validated = $request->validate([
            'currency' => ['required', 'string', 'max:8'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items_text' => ['required', 'string'],
        ]);

        $discount = (float) ($validated['discount'] ?? 0);
        $subtotal = $parsed['subtotal'];
        $total = max(0, $subtotal - $discount);

        $quote = Quote::query()->create([
            'deal_id' => $dealModel->id,
            'quote_number' => $this->generateQuoteNumber(),
            'items_json' => $parsed['items'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total_price' => $total,
            'currency' => strtoupper($validated['currency']),
            'status' => 'DRAFT',
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($dealModel->stage === 'NEW') {
            $dealModel->update(['stage' => 'QUOTED']);
            if ($dealModel->lead) {
                $dealModel->lead->update(['status' => 'QUOTED']);
            }
        }

        return redirect()
            ->route('admin.quotes.show', ['quote' => $quote->id])
            ->with('status', 'Quote created successfully.');
    }

    public function update(Request $request, int $quote): RedirectResponse
    {
        $quoteModel = Quote::query()->with('deal.lead')->findOrFail($quote);
        $parsed = $this->parseItemsText((string) $request->input('items_text', ''));

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'currency' => ['required', 'string', 'max:8'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items_text' => ['required', 'string'],
        ]);

        $discount = (float) ($validated['discount'] ?? 0);
        $subtotal = $parsed['subtotal'];
        $total = max(0, $subtotal - $discount);

        $quoteModel->update([
            'status' => $validated['status'],
            'currency' => strtoupper($validated['currency']),
            'discount' => $discount,
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'items_json' => $parsed['items'],
            'subtotal' => $subtotal,
            'total_price' => $total,
            'sent_at' => $validated['status'] === 'SENT' && ! $quoteModel->sent_at ? now() : $quoteModel->sent_at,
        ]);

        return redirect()
            ->route('admin.quotes.show', ['quote' => $quoteModel->id])
            ->with('status', 'Quote updated successfully.');
    }

    private function parseItemsText(string $itemsText): array
    {
        $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $itemsText) ?: []));
        $items = [];
        $subtotal = 0.0;

        foreach ($lines as $line) {
            $parts = array_map('trim', explode(',', $line));
            if (count($parts) < 3) {
                continue;
            }

            $name = $parts[0];
            $quantity = (float) $parts[1];
            $unitPrice = (float) $parts[2];
            $lineTotal = $quantity * $unitPrice;

            if ($name === '' || $quantity <= 0 || $unitPrice < 0) {
                continue;
            }

            $items[] = [
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($lineTotal, 2),
            ];

            $subtotal += $lineTotal;
        }

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items_text' => 'At least one valid quote item is required. Use format: Item Name, Quantity, Unit Price',
            ]);
        }

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
        ];
    }

    private function itemsToText(array $items): string
    {
        $lines = [];
        foreach ($items as $item) {
            $lines[] = implode(', ', [
                $item['name'] ?? '',
                $item['quantity'] ?? '',
                $item['unit_price'] ?? '',
            ]);
        }

        return implode(PHP_EOL, $lines);
    }

    private function generateQuoteNumber(): string
    {
        $count = (int) Quote::query()->whereDate('created_at', now()->toDateString())->count() + 1;
        return 'DGQ-'.now()->format('Ymd').'-'.str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}
