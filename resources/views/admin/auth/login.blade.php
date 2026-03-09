<x-layouts.admin title="Admin Login | Dubai Garments AI">
    <section class="dg-admin-page">
        <div class="dg-auth-wrap">
            <x-ui.card class="dg-auth-card">
                <div class="dg-admin-page-head">
                    <div>
                        <h1 class="dg-page-title">Admin Login</h1>
                        <p class="dg-page-subtitle">Sign in to access leads, pipeline, and quote operations.</p>
                    </div>
                    <x-ui.badge>Secure Area</x-ui.badge>
                </div>

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
                    <div class="dg-admin-toolbar">
                        <x-ui.button type="submit">Login to Console</x-ui.button>
                        <x-ui.button variant="secondary" :href="route('storefront.home')">Back to Store</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </section>
</x-layouts.admin>
