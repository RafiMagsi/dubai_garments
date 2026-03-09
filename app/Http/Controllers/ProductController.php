<?php

namespace App\Http\Controllers;

use App\Support\CatalogData;
use Illuminate\Http\Request;

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
        ]);
    }
}
