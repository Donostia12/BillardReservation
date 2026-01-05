<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Billard Reservation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-950 text-white antialiased h-screen flex flex-col">

    @include('home.header')

    <main class="relative flex-grow flex items-center justify-center p-4 overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('Images/background.webp') }}');">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 opacity-90"></div>
        </div>

        <div
            class="relative z-10 w-full max-w-md bg-slate-900/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8 shadow-2xl overflow-hidden">
            <!-- Decorative Glow -->
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
            </div>
            <div
                class="absolute bottom-0 left-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2">
            </div>

            <div class="relative z-10">
                <div class="text-center mb-8">
                    <h2
                        class="text-3xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent mb-2">
                        Welcome Back</h2>
                    <p class="text-slate-400">Sign in to your account to book a table.</p>
                </div>

                <form action="/login" method="POST" class="space-y-6">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email
                            Address</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all text-white placeholder-slate-500"
                            placeholder="you@example.com">
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                            <a href="#"
                                class="text-xs text-emerald-400 hover:text-emerald-300 transition-colors">Forgot
                                password?</a>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all text-white placeholder-slate-500"
                            placeholder="••••••••">
                    </div>

                    <!-- Remember Me -->
                    {{-- <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-slate-900">
                        <label for="remember" class="ml-2 block text-sm text-slate-400">Remember me</label>
                    </div> --}}

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/20 transform transition-all duration-200 hover:-translate-y-0.5">
                        Sign In
                    </button>

                    <!-- Sign Up Link -->
                    <div class="text-center text-sm text-slate-400 mt-6">
                        Don't have an account?
                        <a href="/register"
                            class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">Create one</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>

</html>