<?php

namespace App\Http\Controllers;

use App\Support\CatalogData;

class StorefrontController extends Controller
{
    public function index(CatalogData $catalogData)
    {
        $categories = $catalogData->categories();
        $products = $catalogData->featuredProducts(8);

        $process = [
            ['title' => 'Select Products', 'description' => 'Choose garment types, fabrics, and customization options.'],
            ['title' => 'Submit Bulk Quote', 'description' => 'Share quantity, design files, delivery date, and notes.'],
            ['title' => 'Review and Confirm', 'description' => 'Receive pricing, timeline, and finalize your order quickly.'],
        ];

        $industries = ['Corporate Events', 'Schools & Universities', 'Sports Teams', 'Hospitality', 'Retail Chains', 'Marketing Agencies'];

        $testimonials = [
            ['name' => 'Ayesha Khan', 'role' => 'Procurement Manager, Atlas Retail', 'quote' => 'Their quote response time and quality consistency made large uniform rollouts much easier for us.'],
            ['name' => 'Fahad Raza', 'role' => 'Event Lead, Summit Expo', 'quote' => 'We ordered 1,200 hoodies and tees for an event and received everything on schedule.'],
        ];

        return view('storefront.home', compact('categories', 'products', 'process', 'industries', 'testimonials'));
    }
}
