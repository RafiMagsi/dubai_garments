<x-layouts.storefront title="Admin Users | Dubai Garments AI">
    <main class="dg-main">
        <section class="dg-section">
            <div class="dg-container">
                <x-ui.card class="dg-info-card">
                    <div class="dg-admin-head">
                        <div>
                            <h1 class="dg-section-title">User Management</h1>
                            <p class="dg-section-copy">Create admin/sales users and manage access roles.</p>
                        </div>
                        <div class="dg-actions-wrap">
                            <x-ui.button variant="secondary" :href="route('admin.leads.index')">Leads</x-ui.button>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit">Logout</x-ui.button>
                            </form>
                        </div>
                    </div>

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

                    <x-ui.card class="dg-summary-card">
                        <h2 class="dg-title-sm">Create User</h2>
                        <form method="POST" action="{{ route('admin.users.store') }}" class="dg-config-form">
                            @csrf
                            <div class="dg-config-grid">
                                <div class="dg-field">
                                    <label class="dg-label" for="name">Name</label>
                                    <x-ui.input id="name" name="name" required />
                                </div>
                                <div class="dg-field">
                                    <label class="dg-label" for="email">Email</label>
                                    <x-ui.input id="email" name="email" type="email" required />
                                </div>
                                <div class="dg-field">
                                    <label class="dg-label" for="role">Role</label>
                                    <select id="role" name="role" class="dg-select" required>
                                        <option value="admin">admin</option>
                                        <option value="sales">sales</option>
                                    </select>
                                </div>
                                <div class="dg-field">
                                    <label class="dg-label" for="password">Password</label>
                                    <x-ui.input id="password" name="password" type="password" required />
                                </div>
                            </div>
                            <div class="dg-hero-actions">
                                <x-ui.button type="submit">Create User</x-ui.button>
                            </div>
                        </form>
                    </x-ui.card>

                    <div class="dg-table-wrap">
                        <table class="dg-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Update Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="dg-status-pill">{{ $user->role }}</span></td>
                                        <td>{{ $user->created_at?->format('M d, Y H:i') ?: '-' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.users.update-role', ['user' => $user->id]) }}" class="dg-form-row">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="dg-select dg-select-md">
                                                    <option value="admin" @selected($user->role === 'admin')>admin</option>
                                                    <option value="sales" @selected($user->role === 'sales')>sales</option>
                                                </select>
                                                <x-ui.button type="submit" variant="secondary">Save</x-ui.button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="dg-hero-actions">
                        {{ $users->links() }}
                    </div>
                </x-ui.card>
            </div>
        </section>
    </main>
</x-layouts.storefront>
