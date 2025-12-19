<header class="fixed w-full z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-700/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="group">
                    <span
                        class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent group-hover:from-emerald-300 group-hover:to-cyan-300 transition-all duration-300">
                        MILLE BILLARD
                    </span>
                    <div class="h-1 w-0 group-hover:w-full bg-emerald-500 transition-all duration-300 rounded-full">
                    </div>
                </a>
            </div>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex space-x-8">
                <a href="/"
                    class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium transition-colors relative group">
                    Home
                    <span
                        class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                </a>
                <a href="/booking"
                    class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium transition-colors relative group">
                    Billard
                    <span
                        class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                </a>
                <a href="#"
                    class="text-gray-300 hover:text-white px-3 py-2 text-sm font-medium transition-colors relative group">
                    Contact
                    <span
                        class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                </a>
                @auth
                    <a href="/dashboard"
                        class="text-emerald-400 hover:text-emerald-300 px-3 py-2 text-sm font-medium transition-colors">
                        Dashboard
                    </a>
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-white px-5 py-2 rounded-full border border-red-500/50 bg-red-500/10 hover:bg-red-500 hover:text-white transition-all duration-300 text-sm font-medium ml-2">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="/login"
                        class="text-white px-5 py-2 rounded-full border border-emerald-500/50 bg-emerald-500/10 hover:bg-emerald-500 hover:text-white transition-all duration-300 text-sm font-medium">
                        Login
                    </a>
                @endauth
            </nav>

            <!-- Mobile Menu Button (Placeholder) -->
            <div class="md:hidden">
                <button class="text-gray-300 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
