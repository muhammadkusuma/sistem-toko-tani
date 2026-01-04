<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Toko Tani</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #059669;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col">
        <nav class="bg-emerald-700 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}"
                            class="flex-shrink-0 flex items-center gap-2 font-bold text-xl hover:text-emerald-100 transition">
                            <i class="fa-solid fa-leaf"></i> TOKO TANI
                        </a>

                        @auth
                            <div class="hidden md:ml-10 md:flex md:space-x-4">
                                @if (Auth::user()->role == 'admin')
                                    <a href="{{ route('products.index') }}"
                                        class="{{ request()->routeIs('products.*') ? 'bg-emerald-800' : 'hover:bg-emerald-600' }} px-3 py-2 rounded-md text-sm font-medium transition">
                                        <i class="fa-solid fa-box mr-1"></i> Data Produk
                                    </a>
                                @endif

                                @if (in_array(Auth::user()->role, ['admin', 'cashier']))
                                    <a href="{{ route('transactions.create') }}"
                                        class="{{ request()->routeIs('transactions.create') ? 'bg-emerald-800' : 'hover:bg-emerald-600' }} px-3 py-2 rounded-md text-sm font-medium transition">
                                        <i class="fa-solid fa-cash-register mr-1"></i> Kasir (POS)
                                    </a>
                                @endif

                                @if (in_array(Auth::user()->role, ['admin', 'owner']))
                                    <a href="{{ route('reports.index') }}"
                                        class="{{ request()->routeIs('reports.*') ? 'bg-emerald-800' : 'hover:bg-emerald-600' }} px-3 py-2 rounded-md text-sm font-medium transition">
                                        <i class="fa-solid fa-chart-line mr-1"></i> Laporan
                                    </a>
                                @endif

                                @if (Auth::user()->role == 'owner')
                                    <a href="{{ route('users.index') }}"
                                        class="{{ request()->routeIs('users.*') ? 'bg-emerald-800' : 'hover:bg-emerald-600' }} px-3 py-2 rounded-md text-sm font-medium transition">
                                        <i class="fa-solid fa-users-gear mr-1"></i> Kelola User
                                    </a>
                                @endif
                            </div>
                        @endauth
                    </div>

                    <div class="flex items-center">
                        @auth
                            <div class="ml-3 relative flex items-center gap-4">
                                <div class="text-right leading-tight hidden sm:block">
                                    <div class="text-sm font-bold">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-emerald-200 capitalize">{{ Auth::user()->role }}</div>
                                </div>

                                <div
                                    class="h-9 w-9 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-800 font-bold border-2 border-emerald-500">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-2 rounded ml-2 transition shadow-sm"
                                        title="Keluar">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm bg-white text-emerald-700 px-4 py-2 rounded font-bold hover:bg-gray-100">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-1 max-w-7xl w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm"
                    role="alert">
                    <i class="fa-solid fa-check-circle mr-2"></i> <span
                        class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm"
                    role="alert">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> <span
                        class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-white border-t mt-auto py-4">
            <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Sistem Toko Tani. All rights reserved.
            </div>
        </footer>
    </div>
</body>

</html>
