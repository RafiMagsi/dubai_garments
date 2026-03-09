<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Support\CatalogData;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class QuoteRequestController extends Controller
{
    public function create(Request $request, CatalogData $catalogData): View
    {
        $categories = $catalogData->categories();
        $products = $catalogData->products();
        $selectedProductSlug = (string) $request->query('product', '');
        $selectedProduct = $selectedProductSlug ? $catalogData->findProduct($selectedProductSlug) : null;
        $savedConfiguration = $selectedProductSlug ? (array) session("product_configurations.$selectedProductSlug", []) : [];

        $prefillQuantity = $savedConfiguration['quantity'] ?? ($selectedProduct['moq'] ?? null);

        $prefillMessage = '';
        if ($selectedProduct) {
            $prefillMessageLines = [
                "Product: {$selectedProduct['name']}",
                'Required quantity: '.($prefillQuantity ?: 'N/A').' pcs',
            ];

            if (! empty($savedConfiguration['color'])) {
                $prefillMessageLines[] = "Preferred color: {$savedConfiguration['color']}";
            }
            if (! empty($savedConfiguration['sizes']) && is_array($savedConfiguration['sizes'])) {
                $prefillMessageLines[] = 'Sizes: '.implode(', ', $savedConfiguration['sizes']);
            }
            if (! empty($savedConfiguration['print_method'])) {
                $prefillMessageLines[] = "Print method: {$savedConfiguration['print_method']}";
            }
            if (! empty($savedConfiguration['delivery_option'])) {
                $prefillMessageLines[] = "Production priority: {$savedConfiguration['delivery_option']}";
            }
            if (! empty($savedConfiguration['notes'])) {
                $prefillMessageLines[] = "Additional notes: {$savedConfiguration['notes']}";
            }

            $prefillMessage = implode("\n", $prefillMessageLines);
        }

        return view('storefront.quote-requests.create', [
            'categories' => $categories,
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'prefillQuantity' => $prefillQuantity,
            'prefillMessage' => $prefillMessage,
        ]);
    }

    public function store(Request $request, CatalogData $catalogData): RedirectResponse
    {
        $productSlugs = array_map(static fn (array $product): string => $product['slug'], $catalogData->products());

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'product_slug' => ['required', 'string', Rule::in($productSlugs)],
            'quantity' => ['required', 'integer', 'min:1'],
            'required_delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'design_file' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg,svg,ai,eps', 'max:10240'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $product = $catalogData->findProduct($validated['product_slug']);
        abort_if(! $product, 422);

        $designFilePath = null;
        if ($request->hasFile('design_file')) {
            $designFilePath = $request->file('design_file')->store('uploads/quote-requests', 'public');
        }

        Lead::create([
            'source' => 'quote_request_form',
            'customer_name' => $validated['customer_name'],
            'company' => $validated['company'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'product_slug' => $validated['product_slug'],
            'product_type' => $product['name'],
            'quantity' => $validated['quantity'],
            'required_delivery_date' => $validated['required_delivery_date'] ?? null,
            'design_file_path' => $designFilePath,
            'message' => $validated['message'],
            'status' => 'NEW',
            'meta' => [
                'category' => $product['category'],
            ],
        ]);

        return redirect()
            ->route('quote-requests.success');
    }

    public function success(CatalogData $catalogData): View
    {
        return view('storefront.quote-requests.success', [
            'categories' => $catalogData->categories(),
        ]);
    }
}
