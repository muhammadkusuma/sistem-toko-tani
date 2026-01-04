<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Toko Tani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full border-t-4 border-emerald-600">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-emerald-700"><i class="fa-solid fa-leaf"></i> Toko Tani</h1>
            <p class="text-gray-500 text-sm mt-2">Silakan login untuk masuk ke sistem</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 border-gray-300 placeholder-gray-400"
                        placeholder="nama@email.com">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required
                        class="w-full pl-10 pr-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 border-gray-300 placeholder-gray-400"
                        placeholder="********">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-emerald-600 text-white font-bold py-2 px-4 rounded hover:bg-emerald-700 transition duration-200 shadow-md">
                MASUK SEKARANG
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Sistem Toko Tani Makmur
        </div>
    </div>

</body>

</html>
