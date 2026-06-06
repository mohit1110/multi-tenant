<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Suspended</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center">
    <div class="max-w-md w-full text-center px-6">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-3">Account Suspended</h1>
        <p class="text-gray-600 mb-8">
            Your organization's account has been suspended. Please contact your administrator or our support team to resolve this.
        </p>
        <a href="{{ route('tenant.login') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
            Back to Login
        </a>
    </div>
</body>
</html>
