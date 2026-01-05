<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Billard Reservation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-white antialiased" x-data="adminDashboard()">

    <nav class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-bold text-xl">A</div>
                    <span class="text-xl font-bold">Admin Dashboard</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400 hidden sm:block">{{ Auth::user()->name }}</span>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-red-400 hover:text-red-300 font-medium px-4 py-2 rounded-lg hover:bg-red-500/10 transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header & Stats -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">Table Management</h1>
                <p class="text-slate-400">Real-time overview of all billiard tables</p>

                <div class="flex flex-wrap gap-3 mt-4">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-sm font-semibold text-emerald-400">Available: {{ $tables->where('status', 'available')->count() }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-yellow-500/10 border border-yellow-500/20">
                        <div class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></div>
                        <span class="text-sm font-semibold text-yellow-400">Pending: {{ $tables->where('status', 'pending')->count() }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-500/10 border border-blue-500/20">
                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                        <span class="text-sm font-semibold text-blue-400">Reserved: {{ $tables->where('status', 'reserved')->count() }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-red-500/10 border border-red-500/20">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="text-sm font-semibold text-red-400">In Use: {{ $tables->where('status', 'used')->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <!-- Tables Grid -->
                <div class="xl:col-span-2">
                    <h2 class="text-xl font-semibold mb-4">Tables Status</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($tables as $table)
                        @php
                        $status = $table->status;
                        $colors = [
                        'available' => ['card' => 'bg-emerald-500/10 border-emerald-500/30', 'text' => 'text-emerald-400', 'dot' => 'bg-emerald-500'],
                        'pending' => ['card' => 'bg-yellow-500/10 border-yellow-500/30', 'text' => 'text-yellow-400', 'dot' => 'bg-yellow-500'],
                        'reserved' => ['card' => 'bg-blue-500/10 border-blue-500/30', 'text' => 'text-blue-400', 'dot' => 'bg-blue-500'],
                        'used' => ['card' => 'bg-red-500/10 border-red-500/30', 'text' => 'text-red-400', 'dot' => 'bg-red-500'],
                        ];
                        $color = $colors[$status] ?? $colors['available'];
                        @endphp

                        <div class="relative {{ $color['card'] }} border rounded-xl p-4 flex flex-col items-center">
                            <div class="w-full aspect-[4/3] relative flex items-center justify-center mb-2">
                                <img src="{{ asset('Images/table.png') }}" alt="Table" class="w-full h-full object-contain opacity-80">
                            </div>

                            <!-- Timer -->
                            @if(($status === 'pending' || $status === 'reserved') && isset($table->expires_at))
                            <div x-data="{
                                    expires: {{ \Carbon\Carbon::parse($table->expires_at)->timestamp * 1000 }},
                                    remaining: '',
                                    init() {
                                        this.tick();
                                        setInterval(() => this.tick(), 1000);
                                    },
                                    tick() {
                                        const now = new Date().getTime();
                                        const distance = this.expires - now;
                                        if (distance < 0) {
                                            this.remaining = '0:00';
                                        } else {
                                            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                            const s = Math.floor((distance % (1000 * 60)) / 1000);
                                            this.remaining = m + ':' + (s < 10 ? '0' : '') + s;
                                        }
                                    }
                                }" x-init="init()" class="absolute top-2 right-2 {{ $status === 'reserved' ? 'bg-blue-500 text-white' : 'bg-yellow-500 text-black' }} px-2 py-1 rounded text-xs font-mono font-bold shadow-lg">
                                <span x-text="remaining"></span>
                            </div>
                            @endif

                            <!-- Table Number -->
                            <h3 class="text-lg font-bold">{{ str_pad($table->number, 2, '0', STR_PAD_LEFT) }}</h3>
                            <p class="text-xs font-medium uppercase {{ $color['text'] }} mt-1">{{ ucfirst($status) }}</p>

                            <!-- Status Dot -->
                            <div class="absolute bottom-2 right-2">
                                <div class="w-2 h-2 rounded-full {{ $color['dot'] }} {{ in_array($status, ['pending', 'reserved']) ? 'animate-pulse' : '' }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Orders Needing Verification -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold">Pending Verification</h2>
                        <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-bold" x-text="orderCount">{{ $pendingOrders->count() }}</span>
                    </div>

                    <div class="space-y-3 max-h-[600px] overflow-y-auto">
                        <template x-for="order in orders" :key="order.id">
                            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-lg">Table <span x-text="String(order.table_number).padStart(2, '0')"></span></h3>
                                        <p class="text-xs text-slate-400" x-text="order.user_name"></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full font-bold"
                                        :class="order.status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-blue-500/20 text-blue-400'"
                                        x-text="order.status.toUpperCase()"></span>
                                </div>

                                <!-- Timer -->
                                <div x-show="order.expires_at" class="mb-3">
                                    <div x-data="{
                                        expires: order.expires_at * 1000,
                                        remaining: '',
                                        init() {
                                            this.tick();
                                            setInterval(() => this.tick(), 1000);
                                        },
                                        tick() {
                                            const now = new Date().getTime();
                                            const distance = this.expires - now;
                                            if (distance < 0) {
                                                this.remaining = 'Expired';
                                            } else {
                                                const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                const s = Math.floor((distance % (1000 * 60)) / 1000);
                                                this.remaining = m + ':' + (s < 10 ? '0' : '') + s;
                                            }
                                        }
                                    }" x-init="init()" class="text-sm text-slate-400">
                                        Time Left: <span class="font-mono font-bold text-yellow-500" x-text="remaining"></span>
                                    </div>
                                </div>

                                <!-- Payment Proof -->
                                <div x-show="order.payment_proof && order.status === 'process'" class="mb-3">
                                    <img :src="order.payment_proof" alt="Payment Proof" class="w-full h-32 object-cover rounded-lg border border-slate-700">
                                </div>

                                <div class="flex gap-2 text-sm">
                                    <span class="text-slate-400">Amount:</span>
                                    <span class="font-bold text-emerald-400" x-text="'Rp ' + order.amount.toLocaleString('id-ID')"></span>
                                </div>

                                <!-- Actions for Process orders -->
                                <template x-if="order.status === 'process'">
                                    <div class="flex gap-2 mt-3">
                                        <form :action="`/admin/orders/${order.id}/approve`" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-semibold text-sm transition-colors">Approve</button>
                                        </form>
                                        <form :action="`/admin/orders/${order.id}/reject`" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold text-sm transition-colors">Reject</button>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div x-show="orderCount === 0" class="text-center text-slate-500 py-12">
                            No pending orders
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification Sound -->
    <audio id="notificationSound" src="{{ asset('notif/notif.mp3') }}" preload="auto"></audio>
    <script>
        function adminDashboard() {
            return {
                orders: @json($ordersData),
                orderCount: {{ $pendingOrders->count() }},
                previousCount: {{ $pendingOrders->count() }},

                init() {
                    // Poll every 10 seconds
                    setInterval(() => this.checkForNewOrders(), 10000);
                },

                async checkForNewOrders() {
                    try {
                        const response = await fetch('/api/admin/pending-orders');
                        const data = await response.json();

                        let shouldNotify = false;
                        let notificationMessage = '';

                        // Check if new orders arrived
                        if (data.count > this.previousCount) {
                            shouldNotify = true;
                            notificationMessage = `${data.count - this.previousCount} new order(s) pending verification`;
                        }
                        // Check if any order changed from pending to process
                        else if (data.count === this.previousCount && this.orders.length > 0) {
                            const oldPendingCount = this.orders.filter(o => o.status === 'pending').length;
                            const newPendingCount = data.orders.filter(o => o.status === 'pending').length;

                            if (newPendingCount < oldPendingCount) {
                                shouldNotify = true;
                                notificationMessage = 'Order payment proof uploaded! Ready for verification';
                            }
                        }

                        if (shouldNotify) {
                            // Play notification sound
                            const audio = document.getElementById('notificationSound');
                            audio.play().catch(e => console.log('Audio play failed:', e));

                            // Show browser notification
                            if ('Notification' in window && Notification.permission === 'granted') {
                                new Notification('Admin Alert!', {
                                    body: notificationMessage,
                                    icon: '/Images/table.png'
                                });
                            }
                        }

                        this.orders = data.orders;
                        this.previousCount = this.orderCount;
                        this.orderCount = data.count;
                    } catch (error) {
                        console.error('Failed to fetch pending orders:', error);
                    }
                },

                mounted() {
                    // Request notification permission
                    if ('Notification' in window && Notification.permission === 'default') {
                        Notification.requestPermission();
                    }
                }
            }
        }
    </script>

</body>

</html>