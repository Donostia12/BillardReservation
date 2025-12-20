<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Billard Reservation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-white antialiased">

    @include('home.header')

    <main class="pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl p-8 border border-slate-800 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                        <p class="text-slate-400">Manage your reservations and profile.</p>
                    </div>
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center text-emerald-400 text-2xl font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Notifications -->
                <div class="lg:col-span-2">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notifications
                        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full ml-2">{{ Auth::user()->notifications->where('is_read', false)->count() }}</span>
                    </h2>

                    <div class="space-y-4">
                        @forelse(Auth::user()->notifications()->latest()->get() as $notification)
                            <div class="bg-slate-900/50 border {{ $notification->is_read ? 'border-slate-800' : 'border-emerald-500/30' }} rounded-xl p-4 transition-all hover:bg-slate-800/50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-white {{ $notification->is_read ? 'opacity-80' : '' }}">{{ $notification->title }}</h3>
                                        <p class="text-sm text-slate-400 mt-1">{{ $notification->message }}</p>
                                    </div>
                                    <span class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 bg-slate-900/30 rounded-xl border border-dashed border-slate-800">
                                <svg class="w-12 h-12 text-slate-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-slate-500">No notifications yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Stats / Quick Actions -->
                <div>
                    <h2 class="text-xl font-bold text-white mb-6">Quick Stats</h2>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-800">
                            <div class="text-slate-400 text-sm mb-1">Total Bookings</div>
                            <div class="text-3xl font-bold text-white">{{ Auth::user()->orders()->count() }}</div>
                        </div>
                        <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-800">
                            <div class="text-slate-400 text-sm mb-1">Active Reservations</div>
                            <div class="text-3xl font-bold text-emerald-400">{{ Auth::user()->orders()->where('status', 'active')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('home.footer')
</body>
</html>
