<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Kios Afgan Admin - Login</title>
</head>

<body class="bg-gray-800 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center">Kios Afgan - Admin Panel</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required autofocus>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded w-full focus:outline-none focus:shadow-outline">
                Login Admin
            </button>
        </form>
    </div>
</body>

</html>