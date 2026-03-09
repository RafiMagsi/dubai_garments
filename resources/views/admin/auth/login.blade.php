<x-layouts.storefront title="Admin Login | Dubai Garments AI">
    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-config-card">
                    <h1 class="dg-section-title">Admin Login</h1>
                    <p class="dg-section-copy">Sign in to access lead management and admin controls.</p>

                    @if ($errors->any())
                        <div class="dg-alert-error">
                            <ul class="dg-error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.login.submit') }}" method="POST" class="dg-config-form">
                        @csrf
                        <div class="dg-field">
                            <label class="dg-label" for="email">Email</label>
                            <x-ui.input id="email" type="email" name="email" :value="old('email')" required />
                        </div>
                        <div class="dg-field">
                            <label class="dg-label" for="password">Password</label>
                            <x-ui.input id="password" type="password" name="password" required />
                        </div>
                        <label class="dg-checkbox-item">
                            <input type="checkbox" name="remember" value="1">
                            <span>Remember me</span>
                        </label>
                        <div class="dg-hero-actions">
                            <x-ui.button type="submit">Login</x-ui.button>
                            <x-ui.button variant="secondary" :href="route('storefront.home')">Back to Store</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </section>
    </main>
</x-layouts.storefront>
