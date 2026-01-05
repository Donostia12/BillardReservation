@php
$table = $tables[$id] ?? null;
$status = $table ? $table->status : 'unavailable'; // Default to unavailable if not found

// Normalize status for comparisons
$status = strtolower($status);

$colors = [
'available' => 'bg-emerald-500/10 border-emerald-500/30 hover:bg-emerald-500/20 text-emerald-400 group-hover:shadow-[0_0_20px_rgba(16,185,129,0.3)]',
'used' => 'bg-red-500/10 border-red-500/30 text-red-400 cursor-not-allowed grayscale-[0.2]', // 'Being Used' mapped to 'used' or 'being used'
'being used' => 'bg-red-500/10 border-red-500/30 text-red-400 cursor-not-allowed grayscale-[0.2]',
'reserved' => 'bg-blue-500/10 border-blue-500/30 text-blue-400 cursor-not-allowed',
'pending' => 'bg-yellow-500/10 border-yellow-500/30 text-yellow-500 cursor-not-allowed',
'unavailable' => 'bg-gray-500/10 border-gray-500/30 text-gray-400 cursor-not-allowed',
];

$indicatorColors = [
'available' => 'bg-emerald-500',
'used' => 'bg-red-500',
'being used' => 'bg-red-500',
'reserved' => 'bg-blue-500',
'pending' => 'bg-yellow-500',
'unavailable' => 'bg-gray-500',
];

// Fall to available if unknown or map correctly
$statusColor = $colors[$status] ?? $colors['unavailable'];
$indicatorColor = $indicatorColors[$status] ?? $indicatorColors['unavailable'];
$isAvailable = $status === 'available';
@endphp

<div class="{{ $statusColor }} relative aspect-[4/3] rounded-2xl border flex flex-col items-center justify-center p-4 transition-all duration-300 group {{ $isAvailable ? 'cursor-pointer hover:-translate-y-1' : '' }}"
    @if($isAvailable)
    onclick="window.location.href='{{ auth()->check() ? route('booking.create', $id) : route('login') }}';"
    @endif>
    <!-- Table Graphic -->
    <div class="w-full h-full relative flex items-center justify-center p-2">
        <img src="{{ asset('Images/table.png') }}" alt="Table" class="w-full h-full object-contain drop-shadow-xl opacity-90 group-hover:opacity-100 transition-opacity">

    </div>

    <!-- Table Number -->
    <div class="absolute text-2xl font-bold opacity-30 group-hover:opacity-100 transition-opacity top-2 left-3">
        {{ str_pad($id, 2, '0', STR_PAD_LEFT) }}
    </div>

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
    }" x-init="init()" class="absolute top-2 right-2 {{ $status === 'reserved' ? 'bg-blue-500/90 text-white' : 'bg-yellow-500/90 text-black' }} px-2 py-1 rounded text-xs font-mono font-bold shadow-lg z-20">
        <span x-text="remaining"></span>
    </div>
    @endif

    <div class="absolute bottom-3 right-3">
        <div class="w-3 h-3 rounded-full {{ $indicatorColor }} shadow-[0_0_8px_rgba(255,255,255,0.5)] animate-pulse"></div>
    </div>

    <!-- Action for Available -->
    @if ($isAvailable)
    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-slate-950/80 backdrop-blur-[2px] rounded-2xl">
        <span class="px-4 py-2 bg-emerald-500 text-white rounded-lg text-sm font-semibold shadow-lg">Book Now</span>
    </div>
    @else
    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-slate-950/80 backdrop-blur-[2px] rounded-2xl">
        <span class="px-4 py-2 bg-slate-700 text-white rounded-lg text-sm font-medium">{{ ucfirst($status) }}</span>
    </div>
    @endif
</div>