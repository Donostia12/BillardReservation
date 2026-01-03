<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - Mille Billard Kuta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
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

            <!-- Tables Layout -->
            <div class="space-y-12">
                <!-- Row 1: Full Width (1-7) -->
                <div>
                    <h2 class="text-xl font-semibold text-slate-400 mb-4">Main Area</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-4">
                        @foreach ($layoutMap['row1'] as $id)
                        @include('booking.table-card', ['id' => $id, 'tables' => $tables])
                        @endforeach
                    </div>
                </div>

                <!-- Row 2: Split (Middle 8-16 | Top Right 23-26) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left Section: 8 tables -->
                    <div class="lg:col-span-8">
                        <h2 class="text-xl font-semibold text-slate-400 mb-4">Middle Section</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach ($layoutMap['row2']['left'] as $id)
                            @include('booking.table-card', ['id' => $id, 'tables' => $tables])
                            @endforeach
                        </div>
                    </div>
                    <!-- Right Section: 4 tables -->
                    <div class="lg:col-span-4">
                        <h2 class="text-xl font-semibold text-slate-400 mb-4">VIP / Upper Right</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($layoutMap['row2']['right'] as $id)
                            @include('booking.table-card', ['id' => $id, 'tables' => $tables])
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Row 3: Split (18, 17 | 27-29) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left Section: 2 tables -->
                    <div class="lg:col-span-4">
                        <h2 class="text-xl font-semibold text-slate-400 mb-4">Side Area</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($layoutMap['row3']['left'] as $id)
                            @include('booking.table-card', ['id' => $id, 'tables' => $tables])
                            @endforeach
                        </div>
                    </div>
                    <!-- Right Section: 3 tables -->
                    <div class="lg:col-span-8 lg:col-start-9">
                        <h2 class="text-xl font-semibold text-slate-400 mb-4">Side Area 2</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach ($layoutMap['row3']['right'] as $id)
                            @include('booking.table-card', ['id' => $id, 'tables' => $tables])
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Row 4: Split (Bar Area 22-19 | Cashier 30, 31) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left Section: Bar Area (4 tables) -->
                    <div class="lg:col-span-6">
                        <h2 class="text-xl font-semibold text-slate-400 mb-4">Bar Area</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach ($layoutMap['row4']['left'] as $id)
                            @include('booking.table-card', ['id' => $id, 'tables' => $tables])
                            @endforeach
                        </div>
                    </div>

                    <!-- Right Section: Cashier Area (2 tables) -->
                    <div class="lg:col-span-3 lg:col-start-10">
                        <h2 class="text-xl font-semibold text-slate-400 mb-4">Cashier Area</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($layoutMap['row4']['right'] as $id)
                            @include('booking.table-card', ['id' => $id, 'tables' => $tables])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="/"
                    class="text-slate-500 hover:text-white transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </main>

    @include('home.footer')
</body>

</html>