<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Table {{ $table->number }} - Mille Billard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-white antialiased">
    @include('home.header')

    <main class="pt-32 pb-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-slate-900 rounded-2xl p-8 border border-slate-800 shadow-xl">
                <div class="mb-8 text-center">
                    <span class="inline-block px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-full text-sm font-medium mb-4">Available Now</span>
                    <h1 class="text-3xl font-bold mb-2">Book Table {{ str_pad($table->number, 2, '0', STR_PAD_LEFT) }}</h1>
                    <p class="text-slate-400">Complete the form below to reserve your spot.</p>
                </div>

                <form action="{{ route('booking.store') }}" method="POST" class="space-y-8"
                    x-data="{ 
                          duration: 1, 
                          rate: 50000,
                          date: '{{ now()->format('Y-m-d') }}',
                          time: '{{ now()->addHour()->format('H:00') }}'
                      }">
                    @csrf
                    <input type="hidden" name="table_id" value="{{ $table->id }}">
                    <!-- Combine Date and Time for Backend -->
                    <input type="hidden" name="start_time" :value="date + 'T' + time">

                    <!-- Start Time Section -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-300">Select Date & Time</label>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Date Input -->
                            <div class="relative group cursor-pointer" onclick="document.getElementById('dateInput').showPicker()">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" id="dateInput" x-model="date" required
                                    onclick="this.showPicker()"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-4 text-white placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all shadow-inner cursor-pointer"
                                    min="{{ now()->format('Y-m-d') }}">
                            </div>

                            <!-- Time Input -->
                            <div class="relative group cursor-pointer" onclick="document.getElementById('timeInput').showPicker()">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <input type="time" id="timeInput" x-model="time" required
                                    onclick="this.showPicker()"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-4 py-4 text-white placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all shadow-inner cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Duration Selection -->
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-slate-300">Duration (Hours)</label>
                        <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl p-1 shadow-inner">
                            <button type="button" @click.prevent="duration > 1 ? duration-- : null"
                                class="p-4 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number" name="duration" x-model.number="duration" min="1" required
                                class="flex-1 bg-transparent border-none text-center text-2xl font-bold text-white focus:ring-0 px-4 py-2 appearance-none">
                            <button type="button" @click.prevent="duration++"
                                class="p-4 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 text-center">Type manually or use +/- buttons</p>
                    </div>

                    <!-- Summary Card -->
                    <div class="bg-slate-950 rounded-xl p-6 border border-slate-800 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-3 opacity-10">
                            <svg class="w-24 h-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 7h6m0 3.667c0 1.657-2.686 3-6 3s-6-1.343-6-3m12 0c0 1.657-2.686 3-6 3s-6-1.343-6-3m12 0c0 1.657-2.686 3-6 3s-6-1.343-6-3m12 0c0 1.657-2.686 3-6 3s-6-1.343-6-3"></path>
                            </svg>
                        </div>

                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400">Duration</span>
                                <span class="font-medium text-white"><span x-text="duration"></span> Hour(s)</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400">Rate</span>
                                <span class="font-medium text-white">Rp 50.000 / hr</span>
                            </div>
                            <div class="border-t border-slate-800 pt-4 flex justify-between items-end">
                                <span class="text-sm font-medium text-slate-300">Total Estimation</span>
                                <span class="text-2xl font-bold text-emerald-400">
                                    Rp <span x-text="(duration * rate).toLocaleString('id-ID')"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('booking.index') }}" class="w-1/3 px-6 py-4 bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 hover:text-white rounded-xl font-semibold transition-all text-center">Cancel</a>
                        <button type="submit" class="w-2/3 px-6 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/20 transition-all transform hover:scale-[1.01] flex items-center justify-center gap-2">
                            Confirm Booking
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @include('home.footer')
</body>

</html>