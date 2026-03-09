<x-layouts.storefront title="Dubai Garments AI | Bulk Garment Storefront">
    <div class="dg-topbar">
        <div class="dg-container dg-topbar-inner">
            <p>Bulk Orders Support: +92 300 0000000</p>
            <p>Email: sales@dubaigarments.ai | Delivery: UAE, KSA, Pakistan</p>
        </div>
    </div>

    <header class="dg-header">
        <div class="dg-container">
            <x-ui.card class="dg-header-inner">
                <div>
                    <p class="dg-brand-subtitle">Dubai Garments</p>
                    <p class="dg-brand-title">Bulk Garment Store</p>
                </div>

                <div class="dg-search-wrap">
                    <x-ui.input type="search" placeholder="Search products, categories, fabrics..." />
                </div>

                <div class="dg-header-actions">
                    <x-ui.button variant="secondary" href="#">Customer Portal</x-ui.button>
                    <x-ui.button href="#">Request Bulk Quote</x-ui.button>
                </div>
            </x-ui.card>

            <nav class="dg-card dg-nav">
                <div class="dg-nav-inner">
                    @foreach ($categories as $category)
                        <a href="#" class="dg-nav-link">{{ $category['name'] }}</a>
                    @endforeach
                    <a href="#" class="dg-nav-link">All Products</a>
                    <a href="#" class="dg-nav-link">Contact</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container dg-hero-grid">
                <x-ui.card class="dg-hero-card">
                    <x-ui.badge>B2B Custom Garments</x-ui.badge>
                    <h1 class="dg-hero-title">Order Branded Apparel in Bulk with Faster Quotations</h1>
                    <p class="dg-section-copy">
                        Browse production-ready garments, submit your quantity and branding requirements, and get a clear quote with timeline,
                        pricing, and follow-up support.
                    </p>
                    <div class="dg-hero-actions">
                        <x-ui.button href="#">Start Quote Request</x-ui.button>
                        <x-ui.button variant="secondary" href="#">Browse Catalog</x-ui.button>
                    </div>
                </x-ui.card>

                <x-ui.card class="dg-quick-card">
                    <p class="dg-eyebrow">Quick Request</p>
                    <h2 class="dg-title-md">Get Quote in Minutes</h2>
                    <div class="dg-quick-list">
                        <p class="dg-quick-item">1. Select product category</p>
                        <p class="dg-quick-item">2. Upload logo or design file</p>
                        <p class="dg-quick-item">3. Share quantity and deadline</p>
                    </div>
                    <x-ui.button href="#" class="dg-btn-block">Submit Bulk Quote</x-ui.button>
                </x-ui.card>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.section-header
                    title="Shop by category"
                    subtitle="Choose a category and request a tailored quote for your bulk order."
                />
                <div class="dg-category-grid">
                    @foreach ($categories as $category)
                        <x-store.category-card :name="$category['name']" :description="$category['description']" />
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.section-header
                    title="Featured products"
                    subtitle="Production-ready garments with clear MOQs, lead times, and customization options."
                >
                    <x-slot:action>
                        <x-ui.button variant="secondary" href="#">View All Products</x-ui.button>
                    </x-slot:action>
                </x-ui.section-header>

                <div class="dg-product-grid">
                    @foreach ($products as $product)
                        <x-store.product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <h2 class="dg-section-title">How bulk ordering works</h2>
                <div class="dg-process-grid">
                    @foreach ($process as $step)
                        <x-ui.card class="dg-category-card">
                            <h3 class="dg-title-sm">{{ $loop->iteration }}. {{ $step['title'] }}</h3>
                            <p class="dg-muted-sm">{{ $step['description'] }}</p>
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container dg-two-col-grid">
                <x-ui.card class="dg-info-card">
                    <h2 class="dg-section-title">Industries served</h2>
                    <p class="dg-section-copy">Built for organizations ordering custom garments at scale.</p>
                    <div class="dg-chip-cloud">
                        @foreach ($industries as $industry)
                            <span class="dg-chip">{{ $industry }}</span>
                        @endforeach
                    </div>
                </x-ui.card>

                <x-ui.card class="dg-info-card">
                    <h2 class="dg-section-title">Trusted by teams</h2>
                    <div class="dg-testimonials">
                        @foreach ($testimonials as $testimonial)
                            <x-store.testimonial-card
                                :quote="$testimonial['quote']"
                                :name="$testimonial['name']"
                                :role="$testimonial['role']"
                            />
                        @endforeach
                    </div>
                </x-ui.card>
            </div>
        </section>

        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-cta-card">
                    <div class="dg-cta-grid">
                        <div>
                            <h2 class="dg-title-lg">Ready to place your bulk garment order?</h2>
                            <p class="dg-muted-sm">Send your requirements and get a quotation with timeline and production plan.</p>
                        </div>
                        <div class="dg-actions-wrap">
                            <x-ui.button href="#">Request Bulk Quote</x-ui.button>
                            <x-ui.button variant="secondary" href="#">Talk to Sales</x-ui.button>
                        </div>
                    </div>
                    <div class="dg-trust-grid">
                        <x-store.trust-item text="Secure Payments" />
                        <x-store.trust-item text="Quality Checked Production" />
                        <x-store.trust-item text="On-Time Bulk Delivery" />
                    </div>
                </x-ui.card>
            </div>
        </section>
    </main>

    <footer class="dg-footer">
        <div class="dg-container dg-footer-grid">
            <div>
                <h3 class="dg-title-sm">Dubai Garments</h3>
                <p class="dg-muted-sm">Bulk custom apparel for businesses, events, and institutions.</p>
            </div>
            <div>
                <h4 class="dg-footer-heading">Store</h4>
                <ul class="dg-footer-list">
                    <li><a href="#">Products</a></li>
                    <li><a href="#">Request Quote</a></li>
                    <li><a href="#">Customer Portal</a></li>
                </ul>
            </div>
            <div>
                <h4 class="dg-footer-heading">Company</h4>
                <ul class="dg-footer-list">
                    <li><a href="#">About</a></li>
                    <li><a href="#">Industries</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="dg-footer-heading">Contact</h4>
                <p class="dg-muted-sm">sales@dubaigarments.ai</p>
                <p class="dg-muted-sm">+92 300 0000000</p>
            </div>
        </div>
    </footer>
</x-layouts.storefront>
