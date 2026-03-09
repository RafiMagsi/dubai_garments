<?php

namespace App\Support;

class CatalogData
{
    public function categories(): array
    {
        return [
            ['name' => 'T-Shirts', 'slug' => 't-shirts', 'description' => 'Corporate and promotional tees'],
            ['name' => 'Hoodies', 'slug' => 'hoodies', 'description' => 'Event and winter collections'],
            ['name' => 'Caps', 'slug' => 'caps', 'description' => 'Branding for campaigns'],
            ['name' => 'Uniforms', 'slug' => 'uniforms', 'description' => 'Operational and staff wear'],
            ['name' => 'Jerseys', 'slug' => 'jerseys', 'description' => 'Custom sports kits'],
            ['name' => 'Jackets', 'slug' => 'jackets', 'description' => 'Premium outerwear orders'],
        ];
    }

    public function products(): array
    {
        $defaultConfiguration = [
            'colors' => ['Black', 'Navy', 'White', 'Grey', 'Maroon'],
            'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'print_methods' => ['Screen Print', 'Embroidery', 'DTF Print'],
            'delivery_options' => [
                ['value' => 'standard', 'label' => 'Standard Production'],
                ['value' => 'priority', 'label' => 'Priority Production'],
            ],
        ];

        return [
            ['slug' => 'premium-cotton-corporate-t-shirt', 'name' => 'Premium Cotton Corporate T-Shirt', 'category' => 'T-Shirts', 'category_slug' => 't-shirts', 'moq' => 100, 'lead_time' => '7-10 days', 'fabric' => '180 GSM Cotton', 'customization' => 'Screen print / Embroidery', 'configuration' => $defaultConfiguration],
            ['slug' => 'fleece-event-hoodie', 'name' => 'Fleece Event Hoodie', 'category' => 'Hoodies', 'category_slug' => 'hoodies', 'moq' => 80, 'lead_time' => '10-14 days', 'fabric' => '300 GSM Fleece', 'customization' => 'DTF print / Embroidery', 'configuration' => $defaultConfiguration],
            ['slug' => 'performance-sports-jersey', 'name' => 'Performance Sports Jersey', 'category' => 'Jerseys', 'category_slug' => 'jerseys', 'moq' => 50, 'lead_time' => '7-12 days', 'fabric' => 'Poly Dry-Fit', 'customization' => 'Sublimation', 'configuration' => [
                'colors' => ['Black', 'Royal Blue', 'Red', 'White', 'Green'],
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                'print_methods' => ['Sublimation', 'Heat Transfer'],
                'delivery_options' => $defaultConfiguration['delivery_options'],
            ]],
            ['slug' => 'structured-promotional-cap', 'name' => 'Structured Promotional Cap', 'category' => 'Caps', 'category_slug' => 'caps', 'moq' => 150, 'lead_time' => '6-9 days', 'fabric' => 'Brushed Cotton', 'customization' => 'Embroidery / Patch', 'configuration' => [
                'colors' => ['Black', 'Navy', 'Beige', 'White'],
                'sizes' => ['Standard'],
                'print_methods' => ['Embroidery', 'Patch'],
                'delivery_options' => $defaultConfiguration['delivery_options'],
            ]],
            ['slug' => 'retail-staff-polo-uniform', 'name' => 'Retail Staff Polo Uniform', 'category' => 'Uniforms', 'category_slug' => 'uniforms', 'moq' => 120, 'lead_time' => '8-12 days', 'fabric' => 'Pique Knit', 'customization' => 'Logo Embroidery', 'configuration' => $defaultConfiguration],
            ['slug' => 'executive-softshell-jacket', 'name' => 'Executive Softshell Jacket', 'category' => 'Jackets', 'category_slug' => 'jackets', 'moq' => 60, 'lead_time' => '12-16 days', 'fabric' => 'Softshell', 'customization' => 'Embroidery', 'configuration' => $defaultConfiguration],
            ['slug' => 'event-volunteer-t-shirt', 'name' => 'Event Volunteer T-Shirt', 'category' => 'T-Shirts', 'category_slug' => 't-shirts', 'moq' => 120, 'lead_time' => '5-8 days', 'fabric' => '160 GSM Cotton', 'customization' => 'Screen print', 'configuration' => $defaultConfiguration],
            ['slug' => 'college-club-hoodie', 'name' => 'College Club Hoodie', 'category' => 'Hoodies', 'category_slug' => 'hoodies', 'moq' => 70, 'lead_time' => '9-13 days', 'fabric' => 'Fleece Blend', 'customization' => 'DTF / Puff print', 'configuration' => $defaultConfiguration],
        ];
    }

    public function featuredProducts(int $limit = 8): array
    {
        return array_slice($this->products(), 0, $limit);
    }

    public function filterProducts(?string $categorySlug, ?string $search): array
    {
        $categorySlug = $categorySlug ? trim($categorySlug) : null;
        $search = $search ? mb_strtolower(trim($search)) : null;

        return array_values(array_filter($this->products(), function (array $product) use ($categorySlug, $search): bool {
            if ($categorySlug && $product['category_slug'] !== $categorySlug) {
                return false;
            }

            if (! $search) {
                return true;
            }

            $haystack = mb_strtolower(implode(' ', [
                $product['name'],
                $product['category'],
                $product['fabric'],
                $product['customization'],
            ]));

            return str_contains($haystack, $search);
        }));
    }

    public function findProduct(string $slug): ?array
    {
        foreach ($this->products() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    public function relatedProducts(string $categorySlug, string $excludeSlug, int $limit = 4): array
    {
        $matches = array_values(array_filter($this->products(), function (array $product) use ($categorySlug, $excludeSlug): bool {
            return $product['category_slug'] === $categorySlug && $product['slug'] !== $excludeSlug;
        }));

        if (count($matches) < $limit) {
            $others = array_values(array_filter($this->products(), fn (array $product): bool => $product['slug'] !== $excludeSlug));
            $matches = array_merge($matches, $others);
        }

        $unique = [];
        foreach ($matches as $item) {
            $unique[$item['slug']] = $item;
        }

        return array_slice(array_values($unique), 0, $limit);
    }
}
