<?php

namespace App\Http\Controllers;

class StorefrontController extends Controller
{
    public function index()
    {
        $categories = [
            ['name' => 'T-Shirts', 'slug' => 't-shirts', 'description' => 'Corporate and promotional tees'],
            ['name' => 'Hoodies', 'slug' => 'hoodies', 'description' => 'Event and winter collections'],
            ['name' => 'Caps', 'slug' => 'caps', 'description' => 'Branding for campaigns'],
            ['name' => 'Uniforms', 'slug' => 'uniforms', 'description' => 'Operational and staff wear'],
            ['name' => 'Jerseys', 'slug' => 'jerseys', 'description' => 'Custom sports kits'],
            ['name' => 'Jackets', 'slug' => 'jackets', 'description' => 'Premium outerwear orders'],
        ];

        $products = [
            ['name' => 'Premium Cotton Corporate T-Shirt', 'category' => 'T-Shirts', 'moq' => 100, 'lead_time' => '7-10 days', 'fabric' => '180 GSM Cotton', 'customization' => 'Screen print / Embroidery'],
            ['name' => 'Fleece Event Hoodie', 'category' => 'Hoodies', 'moq' => 80, 'lead_time' => '10-14 days', 'fabric' => '300 GSM Fleece', 'customization' => 'DTF print / Embroidery'],
            ['name' => 'Performance Sports Jersey', 'category' => 'Jerseys', 'moq' => 50, 'lead_time' => '7-12 days', 'fabric' => 'Poly Dry-Fit', 'customization' => 'Sublimation'],
            ['name' => 'Structured Promotional Cap', 'category' => 'Caps', 'moq' => 150, 'lead_time' => '6-9 days', 'fabric' => 'Brushed Cotton', 'customization' => 'Embroidery / Patch'],
            ['name' => 'Retail Staff Polo Uniform', 'category' => 'Uniforms', 'moq' => 120, 'lead_time' => '8-12 days', 'fabric' => 'Pique Knit', 'customization' => 'Logo Embroidery'],
            ['name' => 'Executive Softshell Jacket', 'category' => 'Jackets', 'moq' => 60, 'lead_time' => '12-16 days', 'fabric' => 'Softshell', 'customization' => 'Embroidery'],
            ['name' => 'Event Volunteer T-Shirt', 'category' => 'T-Shirts', 'moq' => 120, 'lead_time' => '5-8 days', 'fabric' => '160 GSM Cotton', 'customization' => 'Screen print'],
            ['name' => 'College Club Hoodie', 'category' => 'Hoodies', 'moq' => 70, 'lead_time' => '9-13 days', 'fabric' => 'Fleece Blend', 'customization' => 'DTF / Puff print'],
        ];

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
