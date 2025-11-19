<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'E-Kinerja' }}</title>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <div class="min-h-screen flex">

        {{-- SIDEBAR --}}
        @include('layouts.sidebar')

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col">

            {{-- HEADER --}}
            @include('layouts.header')

            {{-- PAGE HEADER SLOT --}}
            @hasSection('page-header')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-4 px-4">
                        @yield('page-header')
                    </div>
                </header>
            @endif

            {{-- PAGE CONTENT --}}
            <main class="p-4">
                {{ $slot }}
            </main>

            {{-- FOOTER --}}
            @include('layouts.footer')

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('sweet_alert'))
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Informasi',
            text: '{{ session("sweet_alert") }}',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    <script> feather.replace();</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>
