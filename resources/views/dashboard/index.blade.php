<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Mille Billard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-white antialiased" x-data="{ activeOrder: null, showModal: false, showHistory: false }">
    @include('home.header')

    <main class="pt-32 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12 flex justify-between items-end">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome Back, {{ auth()->user()->name }}</h1>
                    <p class="text-slate-400">Manage your reservations and profile.</p>
                </div>
                <div class="flex gap-4">
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 text-center min-w-[140px]">
                        <div class="text-3xl font-bold text-emerald-400">{{ $orders->whereIn('status', ['active', 'process'])->count() }}</div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-1">Active / Process</div>
                    </div>
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 text-center min-w-[140px] cursor-pointer hover:bg-slate-800 transition-colors" @click="showHistory = true">
                        <div class="text-3xl font-bold text-blue-400">{{ $orders->where('status', 'completed')->count() }}</div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-1 flex items-center justify-center gap-1">
                            History <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Active & Processing Reservations (Left Column) -->
                <div class="lg:col-span-2">
                    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Current Reservations
                    </h2>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                        @forelse($orders->whereIn('status', ['active', 'process', 'pending']) as $order)
                        <!-- Clickable Row -->
                        <div class="p-6 border-b border-slate-800 last:border-0 hover:bg-slate-800/50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer group"
                            @click="activeOrder = { 
                                id: {{ $order->id }}, 
                                number: {{ $order->billiardTable->number }}, 
                                status: '{{ $order->status }}',
                                payment_proof: '{{ $order->payment_proof ? asset('storage/' . $order->payment_proof) : '' }}'
                             }; showModal = true">

                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 bg-slate-800 rounded-xl flex items-center justify-center border border-slate-700 font-bold text-2xl text-slate-500 group-hover:border-emerald-500/50 group-hover:text-emerald-500 transition-colors">
                                    {{ str_pad($order->billiardTable->number, 2, '0', STR_PAD_LEFT) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-lg">Table {{ $order->billiardTable->number }}</h3>
                                        @if($order->status == 'process')
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-400 text-xs rounded-full font-bold border border-yellow-500/20">PROCESS</span>
                                            @if($order->expires_at)
                                            <span x-data="{
                                                expires: {{ $order->expires_at->timestamp * 1000 }},
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
                                            }" x-init="init()" class="text-xs font-mono text-yellow-500" x-text="remaining"></span>
                                            @endif
                                        </div>
                                        @elseif($order->status == 'pending')
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-gray-500/10 text-gray-400 text-xs rounded-full font-bold border border-gray-500/20">PENDING</span>
                                            @if($order->expires_at)
                                            <span x-data="{
                                                expires: {{ $order->expires_at->timestamp * 1000 }},
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
                                            }" x-init="init()" class="text-xs font-mono text-yellow-500" x-text="remaining"></span>
                                            @endif
                                        </div>
                                        @else
                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-xs rounded-full font-bold border border-emerald-500/20">ACTIVE</span>
                                        @endif
                                    </div>
                                    <div class="text-slate-400 text-sm">
                                        {{ \Carbon\Carbon::parse($order->start_time)->format('M d, Y • H:i') }}
                                        <span class="mx-2">•</span>
                                        {{ $order->end_time ? \Carbon\Carbon::parse($order->end_time)->diffInHours($order->start_time) : '?' }} hrs
                                    </div>
                                    <div class="text-xs text-blue-400 mt-1 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span x-text="'{{ $order->status }}' === 'pending' ? 'Click to Upload Proof' : 'View Payment Proof'"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">Rp {{ number_format($order->amount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="p-12 text-center text-slate-500">
                            No active or processing reservations.
                            <a href="{{ route('booking.index') }}" class="text-emerald-400 hover:underline ml-1">Book a table</a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Notifications (Right Column) -->
                <div>
                    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Notifications
                    </h2>
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden max-h-[500px] overflow-y-auto">
                        @forelse($notifications as $notification)
                        <div class="p-4 border-b border-slate-800 last:border-0 hover:bg-slate-800/30 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-semibold text-sm {{ $notification->is_read ? 'text-slate-400' : 'text-white' }}">{{ $notification->title }}</h4>
                                <span class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-400">{{ $notification->message }}</p>
                        </div>
                        @empty
                        <div class="p-6 text-center text-slate-500 text-sm">No new notifications.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Upload/View Proof Modal -->
    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        style="display: none;">

        <div @click.away="showModal = false"
            class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-md shadow-2xl transform transition-all">

            <div class="flex justify-between items-center mb-6">
                <!-- Dynamic Title -->
                <h3 class="text-xl font-bold">
                    <span x-show="activeOrder?.status === 'pending'">Upload Payment Proof</span>
                    <span x-show="activeOrder?.status === 'process'">Payment Proof (Processing)</span>
                    <span x-show="activeOrder?.status === 'active'">Active Booking</span>
                </h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content for Pending (Upload Form) -->
            <div x-show="activeOrder?.status === 'pending'">
                <p class="text-slate-400 text-sm mb-4">
                    Please upload the payment proof for <strong>Table <span x-text="activeOrder?.number"></span></strong> to proceed.
                </p>

                <form x-bind:action="'/booking/' + activeOrder?.id + '/upload'" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="relative group">
                        <input type="file" name="payment_proof" required accept="image/*"
                            class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 cursor-pointer">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showModal = false" class="w-full px-4 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-semibold transition-colors">Cancel</button>
                        <button type="submit" class="w-full px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/20 transition-all">Upload</button>
                    </div>
                </form>
            </div>

            <!-- Content for Process/Active (View Image) -->
            <div x-show="activeOrder?.status === 'process' || activeOrder?.status === 'active'">
                <p class="text-slate-400 text-sm mb-4" x-show="activeOrder?.status === 'process'">
                    Proof submitted. Waiting for admin verification.
                </p>
                <div class="w-full rounded-xl overflow-hidden border border-slate-800 bg-black flex items-center justify-center p-2 mb-4">
                    <template x-if="activeOrder?.payment_proof">
                        <img :src="activeOrder.payment_proof" alt="Payment Proof" class="w-full h-auto object-contain max-h-96 rounded-lg">
                    </template>
                    <template x-if="!activeOrder?.payment_proof">
                        <span class="text-slate-500 text-xs py-12">No image found</span>
                    </template>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-full px-4 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-semibold transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div x-show="showHistory"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        style="display: none;">

        <div @click.away="showHistory = false"
            class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-2xl shadow-2xl transform transition-all h-[80vh] flex flex-col">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Booking History
                </h3>
                <button @click="showHistory = false" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto pr-2 space-y-4">
                @forelse($orders->whereIn('status', ['completed', 'cancelled', 'rejected']) as $order)
                <div class="p-4 border border-slate-800 rounded-xl flex justify-between items-center text-sm bg-slate-950/50">
                    <div class="flex items-center gap-4">
                        <span class="font-mono text-slate-500">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <div class="font-semibold text-base mb-0.5">Table {{ $order->billiardTable->number }}</div>
                            <div class="text-slate-400 text-xs">{{ \Carbon\Carbon::parse($order->start_time)->format('D, d M Y • H:i') }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold mb-0.5">Rp {{ number_format($order->amount, 0, ',', '.') }}</div>
                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-full border 
                            {{ $order->status == 'completed' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20' }}">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-slate-500 flex flex-col items-center">
                    <svg class="w-12 h-12 text-slate-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>No history found.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @include('home.footer')
</body>

</html>