<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Billard Reservation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-slate-800 antialiased font-sans">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center gap-2">
                         <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-bold">B</div>
                        <span class="text-xl font-bold text-gray-800">Billiard Admin</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                     <span class="text-sm text-gray-500 hidden sm:block">Welcome, {{ Auth::user()->name }}</span>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium px-4 py-2 rounded-lg hover:bg-red-50 transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header & Stats -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Table Management</h1>
                    <p class="text-gray-500 text-sm mt-1">Overview of all billiard tables status.</p>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-xs font-semibold">Available: {{ $tables->where('status', 'available')->count() }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <span class="text-xs font-semibold">Reserved: {{ $tables->where('status', 'reserved')->count() }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-100 text-red-800 border border-red-200">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="text-xs font-semibold">Used: {{ $tables->where('status', 'used')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($tables as $table)
                    @php
                        $status = $table->status;
                        
                        // Define styles based on status
                        $colors = match($status) {
                            'available' => [
                                'card' => 'bg-white border-emerald-200 hover:border-emerald-400 hover:shadow-emerald-100',
                                'text' => 'text-emerald-600',
                                'bg_indicator' => 'bg-emerald-500',
                                'felt' => 'bg-slate-800',
                                'overlay' => 'bg-emerald-500/10'
                            ],
                            'used' => [
                                'card' => 'bg-white border-red-200 hover:border-red-400 hover:shadow-red-100',
                                'text' => 'text-red-600',
                                'bg_indicator' => 'bg-red-500',
                                'felt' => 'bg-slate-800',
                                'overlay' => 'bg-red-500/10'
                            ],
                            'reserved' => [
                                'card' => 'bg-white border-blue-200 hover:border-blue-400 hover:shadow-blue-100',
                                'text' => 'text-blue-600',
                                'bg_indicator' => 'bg-blue-500',
                                'felt' => 'bg-slate-800',
                                'overlay' => 'bg-blue-500/10'
                            ],
                            default => [
                                'card' => 'bg-white border-gray-200',
                                'text' => 'text-gray-600',
                                'bg_indicator' => 'bg-gray-500',
                                'felt' => 'bg-slate-800',
                                'overlay' => 'bg-gray-500/10'
                            ]
                        };
                    @endphp

                    <div class="relative group {{ $colors['card'] }} border rounded-2xl p-4 transition-all duration-300 shadow-sm hover:shadow-lg flex flex-col items-center">
                        
                        <!-- Table Graphic -->
                        <div class="w-full aspect-[4/3] relative flex items-center justify-center mb-3">
                            <div class="w-24 h-14 {{ $colors['felt'] }} rounded-lg shadow-inner border border-slate-700 relative overflow-hidden transform group-hover:scale-110 transition-transform duration-300">
                                <!-- Status Overlay -->
                                <div class="absolute inset-0 {{ $colors['overlay'] }}"></div>
                                <div class="absolute inset-x-4 top-0 h-full border-x border-dashed border-white/10"></div>
                                
                                <!-- Billiard Balls (Simple representation) -->
                                @if($status === 'used')
                                    <div class="absolute top-1/2 left-1/3 w-1.5 h-1.5 bg-white rounded-full shadow-sm"></div>
                                    <div class="absolute top-1/2 left-1/2 w-1.5 h-1.5 bg-yellow-400 rounded-full shadow-sm"></div>
                                    <div class="absolute top-2/3 left-1/2 w-1.5 h-1.5 bg-red-600 rounded-full shadow-sm"></div>
                                @endif
                            </div>
                            
                            <!-- Status Pulsing Dot -->
                            <div class="absolute bottom-2 right-4">
                                <span class="flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $colors['bg_indicator'] }} opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 {{ $colors['bg_indicator'] }}"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Table Info -->
                        <div class="w-full text-center">
                            <h3 class="text-lg font-bold text-gray-800">Table {{ str_pad($table->number, 2, '0', STR_PAD_LEFT) }}</h3>
                            <p class="text-xs font-medium uppercase tracking-wider mt-1 {{ $colors['text'] }}">
                                {{ ucfirst($status) }}
                            </p>
                        </div>

                        <!-- Action Overlay (Edit Placeholder) -->
                        <div class="absolute inset-x-0 bottom-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0 duration-300">
                             <button class="w-full py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-colors shadow-lg">
                                Manage
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
        </div>
    </main>

</body>
</html>
