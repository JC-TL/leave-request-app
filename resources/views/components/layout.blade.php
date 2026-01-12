<!DOCTYPE html>
<html lang="en" data-theme="lofi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - Leave Request Platform' : 'Leave Request Platform' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-base-200 font-sans" 
      style="background-image: url('{{ asset('images/bg.png') }}'); 
             background-size: cover; 
             background-position: center;
             background-attachment: fixed;
             opacity: 110%">

    @isset($navigation)
        {{ $navigation }}
    @endisset

    <main class="flex-1 max-w-3xl mx-auto px-6 py-6">
        {{ $slot }}
    </main>

    <footer class="footer footer-center p-5 bg-base-300 text-base-content text-xs mt-auto">
        <div>
            <p>© Vertex Global 2026</p>
        </div>
    </footer>
</body>

</html>
