<?php

namespace App\Http\Controllers;

use App\Support\CatalogData;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request, CatalogData $catalogData)
    {
        $categories = $catalogData->categories();
        $activeCategory = (string) $request->query('category', '');
        $search = (string) $request->query('search', '');

        $products = $catalogData->filterProducts($activeCategory ?: null, $search ?: null);

        return view('storefront.products.index', [
            'categories' => $categories,
            'products' => $products,
            'activeCategory' => $activeCategory,
            'search' => $search,
        ]);
    }

    public function show(string $slug, CatalogData $catalogData)
    {
        $product = $catalogData->findProduct($slug);
        abort_if(! $product, 404);

        return view('storefront.products.show', [
            'product' => $product,
            'categories' => $catalogData->categories(),
            'relatedProducts' => $catalogData->relatedProducts($product['category_slug'], $slug),
            'savedConfiguration' => session("product_configurations.$slug", []),
        ]);
    }

    public function configure(Request $request, string $slug, CatalogData $catalogData): RedirectResponse
    {
        $product = $catalogData->findProduct($slug);
        abort_if(! $product, 404);

        $config = $product['configuration'] ?? [];
        $allowedColors = $config['colors'] ?? [];
        $allowedSizes = $config['sizes'] ?? [];
        $allowedPrintMethods = $config['print_methods'] ?? [];
        $allowedDelivery = array_column($config['delivery_options'] ?? [], 'value');

        $validated = $request->validate([
            'color' => ['required', 'string', Rule::in($allowedColors)],
            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*' => ['string', Rule::in($allowedSizes)],
            'print_method' => ['required', 'string', Rule::in($allowedPrintMethods)],
            'delivery_option' => ['required', 'string', Rule::in($allowedDelivery)],
            'quantity' => ['required', 'integer', "min:{$product['moq']}"],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        session()->put("product_configurations.$slug", $validated);

        return redirect()
            ->route('products.show', ['slug' => $slug])
            ->with('status', 'Product configuration saved. You can proceed to request a quote.');
    }
}
