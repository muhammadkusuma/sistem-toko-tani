<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Toko Tani</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Hapus panah di input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <nav class="bg-emerald-700 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="{{ url('/') }}" class="flex items-center space-x-2 font-bold text-xl">
                    <i class="fa-solid fa-leaf"></i>
                    <span>Toko Tani</span>
                </a>

                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-200 transition">
                        <i class="fa-solid fa-gauge mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('products.index') }}" class="hover:text-emerald-200 transition">
                        <i class="fa-solid fa-box-open mr-1"></i> Produk
                    </a>
                    <a href="{{ route('transactions.create') }}" class="hover:text-emerald-200 transition">
                        <i class="fa-solid fa-cash-register mr-1"></i> Kasir (POS)
                    </a>
                    <a href="{{ route('reports.index') }}" class="hover:text-emerald-200 transition">
                        <i class="fa-solid fa-file-invoice mr-1"></i> Laporan
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-sm hidden md:block">
                        Halo, Admin
                    </div>
                    <button class="md:hidden focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 min-h-screen">
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                });
            </script>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t mt-auto py-6">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} Sistem Toko Tani. All rights reserved.
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
