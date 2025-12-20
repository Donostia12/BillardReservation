<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - Kuta Billard</title>
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
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-white mb-4">Select Your Table</h1>
                <p class="text-slate-400">Please refresh to update availability.</p>
                
                <!-- Legend -->
                <div class="flex justify-center gap-6 mt-8">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-sm text-slate-300">Available</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
                        <span class="text-sm text-slate-300">Being Used</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                        <span class="text-sm text-slate-300">Reserved</span>
                    </div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @php
                    // Simulate table statuses for 30 tables
                    // In a real app, this would come from the database
                    $statuses = ['available', 'Being Used', 'reserved'];
                @endphp

                @for ($i = 1; $i <= 30; $i++)
                    @php
                        // Randomly assign a status
                        $status = $statuses[array_rand($statuses)];
                        // Force some specific distribution if needed, but random is fine for visual demo
                        
                        $colors = [
                            'available' => 'bg-emerald-500/10 border-emerald-500/30 hover:bg-emerald-500/20 text-emerald-400 group-hover:shadow-[0_0_20px_rgba(16,185,129,0.3)]',
                            'Being Used' => 'bg-red-500/10 border-red-500/30 text-red-400 cursor-not-allowed grayscale-[0.2]',
                            'reserved' => 'bg-blue-500/10 border-blue-500/30 text-blue-400 cursor-not-allowed',
                        ];
                        
                        $statusColor = $colors[$status];
                        
                        $indicatorColor = match($status) {
                            'available' => 'bg-emerald-500',
                            'Being Used' => 'bg-red-500',
                            'reserved' => 'bg-blue-500',
                        };

                        $isAvailable = $status === 'available';
                    @endphp

                    <div class="{{ $statusColor }} relative aspect-[4/3] rounded-2xl border flex flex-col items-center justify-center p-4 transition-all duration-300 group {{ $isAvailable ? 'cursor-pointer hover:-translate-y-1' : '' }}">
                        <!-- Table Graphic -->
                        <div class="w-full h-full relative flex items-center justify-center">
                            <!-- Felt Area -->
                            <div class="w-24 h-14 bg-slate-800 rounded-lg shadow-inner border border-slate-700 relative overflow-hidden">
                                <!-- Status Overlay on Table -->
                                <div class="absolute inset-0 {{ str_replace('bg-', 'bg-', $indicatorColor) }}/20"></div>
                                <div class="absolute inset-x-4 top-0 h-full border-x border-dashed border-white/10"></div>
                            </div>
                        </div>

                        <!-- Table Number -->
                        <div class="absolute text-2xl font-bold opacity-30 group-hover:opacity-100 transition-opacity top-2 left-3">
                            {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                        </div>

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
                @endfor
            </div>
            
            <div class="mt-12 text-center">
                 <a href="/" class="text-slate-500 hover:text-white transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </main>

    @include('home.footer')
</body>
</html>
