<!DOCTYPE html>
<html lang="en" data-theme="lofi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - Leave Request Platform' : 'Leave Request Platform' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js'></script>
</head>

<body class="min-h-screen flex flex-col bg-base-200 font-sans" 
      style="background-image: url('{{ asset('images/bg.png') }}');
             background-size: cover; 
             background-position: center;
             background-attachment: fixed;
             opacity: 100%">

    @isset($navigation)
        {{ $navigation }}
    @endisset

    <main class="flex-1 w-full px-6 py-6 {{ !isset($navigation) ? 'max-w-md mx-auto' : '' }}">
        {{ $slot }}
    </main>

        <div class="text-center text-xs mt-5 opacity-50">
            <p>© Vertex Global 2026</p>
        </div>
</body>

</html>
