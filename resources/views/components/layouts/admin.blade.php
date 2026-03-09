<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin | Dubai Garments AI' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen font-sans antialiased">
        @php
            $currentUser = auth()->user();
        @endphp
        <div class="dg-admin-shell">
            <aside class="dg-admin-sidebar">
                <div class="dg-admin-brand">
                    <p class="dg-brand-subtitle">Dubai Garments</p>
                    <p class="dg-brand-title">Sales Console</p>
                    <p class="dg-admin-brand-copy">Lead, deal, and quote operations in one workspace.</p>
                </div>

                @if ($currentUser)
                    <nav class="dg-admin-nav" aria-label="Admin Navigation">
                        <a href="{{ route('admin.dashboard') }}" class="dg-admin-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                            <span>Dashboard</span>
                            <small>Analytics</small>
                        </a>
                        <a href="{{ route('admin.leads.index') }}" class="dg-admin-link {{ request()->routeIs('admin.leads.*') ? 'is-active' : '' }}">
                            <span>Leads</span>
                            <small>Qualification</small>
                        </a>
                        <a href="{{ route('admin.deals.index') }}" class="dg-admin-link {{ request()->routeIs('admin.deals.*') ? 'is-active' : '' }}">
                            <span>Deals</span>
                            <small>Pipeline</small>
                        </a>
                        <a href="{{ route('admin.quotes.index') }}" class="dg-admin-link {{ request()->routeIs('admin.quotes.*') ? 'is-active' : '' }}">
                            <span>Quotes</span>
                            <small>Pricing</small>
                        </a>
                        <a href="{{ route('admin.followups.index') }}" class="dg-admin-link {{ request()->routeIs('admin.followups.*') ? 'is-active' : '' }}">
                            <span>Follow-ups</span>
                            <small>Automation</small>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="dg-admin-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                            <span>Users</span>
                            <small>Access</small>
                        </a>
                    </nav>
                @else
                    <nav class="dg-admin-nav" aria-label="Admin Navigation">
                        <a href="{{ route('admin.login') }}" class="dg-admin-link is-active">
                            <span>Admin Login</span>
                            <small>Secure access</small>
                        </a>
                    </nav>
                @endif

                <div class="dg-admin-footer">
                    <a href="{{ route('storefront.home') }}" class="dg-btn-secondary">Open Storefront</a>
                    @if ($currentUser)
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dg-btn-secondary dg-btn-block">Logout</button>
                        </form>
                    @endif
                </div>
            </aside>

            <main class="dg-admin-main">
                <header class="dg-admin-topbar">
                    <div>
                        <p class="dg-admin-topbar-label">Admin Workspace</p>
                        <p class="dg-admin-topbar-title">Dubai Garments CRM</p>
                    </div>
                    <div class="dg-admin-user-pill">
                        <span class="dg-admin-user-avatar">{{ strtoupper(substr($currentUser->name ?? 'G', 0, 1)) }}</span>
                        <div>
                            <p>{{ $currentUser->name ?? 'Guest Session' }}</p>
                            <small>{{ $currentUser->role ?? 'auth required' }}</small>
                        </div>
                    </div>
                </header>

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
