<x-layouts.storefront title="{{ $product['name'] }} | Dubai Garments AI">
    <x-store.header :categories="$categories" search="" />

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container dg-detail-grid">
                <x-ui.card class="dg-detail-media">
                    <div class="dg-product-image"></div>
                </x-ui.card>

                <x-ui.card class="dg-info-card">
                    <x-ui.badge>{{ $product['category'] }}</x-ui.badge>
                    <h1 class="dg-title-lg">{{ $product['name'] }}</h1>
                    <p class="dg-muted-sm">MOQ: {{ $product['moq'] }} pcs</p>
                    <p class="dg-muted-sm">Lead Time: {{ $product['lead_time'] }}</p>
                    <p class="dg-muted-sm">Fabric: {{ $product['fabric'] }}</p>
                    <p class="dg-muted-sm">Customization: {{ $product['customization'] }}</p>
                    <div class="dg-hero-actions">
                        <x-ui.button href="#">Request Quote</x-ui.button>
                        <x-ui.button variant="secondary" :href="route('products.index', ['category' => $product['category_slug']])">More {{ $product['category'] }}</x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-config-card">
                    <h2 class="dg-section-title">Product Configuration</h2>
                    <p class="dg-section-copy">Select product preferences to prepare an accurate quote.</p>

                    @if (session('status'))
                        <div class="dg-alert-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="dg-alert-error">
                            <ul class="dg-error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $config = $product['configuration'];
                        $selectedSizes = old('sizes', $savedConfiguration['sizes'] ?? []);
                    @endphp

                    <form action="{{ route('products.configure', ['slug' => $product['slug']]) }}" method="POST" class="dg-config-form">
                        @csrf

                        <div class="dg-config-grid">
                            <div class="dg-field">
                                <label class="dg-label" for="color">Color</label>
                                <select id="color" name="color" class="dg-select" required>
                                    <option value="">Select color</option>
                                    @foreach ($config['colors'] as $color)
                                        <option value="{{ $color }}" @selected(old('color', $savedConfiguration['color'] ?? '') === $color)>
                                            {{ $color }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="print_method">Print Method</label>
                                <select id="print_method" name="print_method" class="dg-select" required>
                                    <option value="">Select method</option>
                                    @foreach ($config['print_methods'] as $method)
                                        <option value="{{ $method }}" @selected(old('print_method', $savedConfiguration['print_method'] ?? '') === $method)>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="quantity">Quantity</label>
                                <x-ui.input
                                    id="quantity"
                                    name="quantity"
                                    type="number"
                                    :value="old('quantity', $savedConfiguration['quantity'] ?? $product['moq'])"
                                    :min="$product['moq']"
                                    required
                                />
                                <p class="dg-help">Minimum quantity: {{ $product['moq'] }} pcs</p>
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="delivery_option">Production Priority</label>
                                <select id="delivery_option" name="delivery_option" class="dg-select" required>
                                    <option value="">Select option</option>
                                    @foreach ($config['delivery_options'] as $delivery)
                                        <option value="{{ $delivery['value'] }}" @selected(old('delivery_option', $savedConfiguration['delivery_option'] ?? '') === $delivery['value'])>
                                            {{ $delivery['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="dg-field">
                            <label class="dg-label">Sizes</label>
                            <div class="dg-checkbox-group">
                                @foreach ($config['sizes'] as $size)
                                    <label class="dg-checkbox-item">
                                        <input type="checkbox" name="sizes[]" value="{{ $size }}" @checked(in_array($size, $selectedSizes, true))>
                                        <span>{{ $size }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="dg-field">
                            <label class="dg-label" for="notes">Additional Notes</label>
                            <textarea id="notes" name="notes" class="dg-textarea" rows="4" placeholder="Share logo placement, packaging, or special production notes...">{{ old('notes', $savedConfiguration['notes'] ?? '') }}</textarea>
                        </div>

                        <div class="dg-hero-actions">
                            <x-ui.button type="submit">Save Configuration</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('products.index')">Back to Catalog</x-ui.button>
                        </div>
                    </form>

                    @if (! empty($savedConfiguration))
                        <x-ui.card class="dg-summary-card">
                            <h3 class="dg-title-sm">Saved Configuration</h3>
                            <div class="dg-summary-list">
                                <p><strong>Color:</strong> {{ $savedConfiguration['color'] ?? '-' }}</p>
                                <p><strong>Sizes:</strong> {{ isset($savedConfiguration['sizes']) ? implode(', ', $savedConfiguration['sizes']) : '-' }}</p>
                                <p><strong>Print Method:</strong> {{ $savedConfiguration['print_method'] ?? '-' }}</p>
                                <p><strong>Quantity:</strong> {{ $savedConfiguration['quantity'] ?? '-' }} pcs</p>
                                <p><strong>Production Priority:</strong> {{ $savedConfiguration['delivery_option'] ?? '-' }}</p>
                                <p><strong>Notes:</strong> {{ $savedConfiguration['notes'] ?? '-' }}</p>
                            </div>
                        </x-ui.card>
                    @endif
                </x-ui.card>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.section-header
                    title="Related Products"
                    subtitle="Similar options available for your bulk order requirements."
                />
                <div class="dg-product-grid">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-store.product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <x-store.footer />
</x-layouts.storefront>
