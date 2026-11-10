<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Forbidden</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-white text-gray-800 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="text-center max-w-md">
        <h1 class="text-6xl font-bold text-gray-900 mb-4">403</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-2">Forbidden / Akses Ditolak</h2>
        <p class="text-sm text-gray-500 mb-6">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}
        </p>
    </div>
</body>
</html>
