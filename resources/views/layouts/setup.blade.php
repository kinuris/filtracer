<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="shortcut icon" href="{{ asset('assets/favicon.svg') }}" type="image/x-icon">

    @yield('header')
    @vite('resources/css/app.css')

    <title>@yield('title')</title>
</head>

<body>
    <div class="flex flex-col bg-gray-100 h-screen">
        <div class="h-20 border-b border-gray-300 px-4 flex place-items-center">
            <img class="h-16" src="{{ asset('assets/filtracer_nolabel.svg') }}" alt="Logo">
        </div>

        <div class="flex-1">
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    @yield('script')
</body>

</html>