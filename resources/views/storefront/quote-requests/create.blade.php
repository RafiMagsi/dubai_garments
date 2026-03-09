<x-layouts.storefront title="Request Bulk Quote | Dubai Garments AI">
    <x-store.header :categories="$categories" search="" />

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-config-card">
                    <h1 class="dg-section-title">Request Bulk Quote</h1>
                    <p class="dg-section-copy">
                        Share your product and delivery requirements. Our sales team will prepare a tailored quotation.
                    </p>

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

                    <form action="{{ route('quote-requests.store') }}" method="POST" enctype="multipart/form-data" class="dg-config-form">
                        @csrf

                        <div class="dg-config-grid">
                            <div class="dg-field">
                                <label class="dg-label" for="customer_name">Full Name</label>
                                <x-ui.input id="customer_name" name="customer_name" :value="old('customer_name')" required />
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="company">Company Name</label>
                                <x-ui.input id="company" name="company" :value="old('company')" />
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="email">Email</label>
                                <x-ui.input id="email" type="email" name="email" :value="old('email')" required />
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="phone">Phone</label>
                                <x-ui.input id="phone" name="phone" :value="old('phone')" required />
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="product_slug">Product</label>
                                <select id="product_slug" name="product_slug" class="dg-select" required>
                                    <option value="">Select product</option>
                                    @foreach ($products as $product)
                                        @php
                                            $selectedValue = old('product_slug', $selectedProduct['slug'] ?? '');
                                        @endphp
                                        <option value="{{ $product['slug'] }}" @selected($selectedValue === $product['slug'])>
                                            {{ $product['name'] }} ({{ $product['category'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="quantity">Quantity</label>
                                <x-ui.input id="quantity" type="number" name="quantity" :value="old('quantity', $prefillQuantity)" min="1" required />
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="required_delivery_date">Required Delivery Date</label>
                                <x-ui.input id="required_delivery_date" type="date" name="required_delivery_date" :value="old('required_delivery_date')" />
                            </div>

                            <div class="dg-field">
                                <label class="dg-label" for="design_file">Logo / Design File</label>
                                <input id="design_file" type="file" name="design_file" class="dg-input" />
                                <p class="dg-help">Accepted: PDF, PNG, JPG, JPEG, SVG, AI, EPS (max 10MB)</p>
                            </div>
                        </div>

                        <div class="dg-field">
                            <label class="dg-label" for="message">Project Details</label>
                            <textarea
                                id="message"
                                name="message"
                                class="dg-textarea"
                                rows="5"
                                placeholder="Example: We need 500 event hoodies in black/navy with embroidered chest logo and front print..."
                                required
                            >{{ old('message', $prefillMessage) }}</textarea>
                        </div>

                        <div class="dg-hero-actions">
                            <x-ui.button type="submit">Submit Quote Request</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('products.index')">Back to Catalog</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </section>
    </main>

    <x-store.footer />
</x-layouts.storefront>
