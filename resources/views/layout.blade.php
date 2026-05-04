<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- GANTI TITLE DI SINI --}}
    <title>SIAP Dakwah | LDK Al-Fath</title>

    {{-- TAMBAHKAN KODE INI UNTUK ICON --}}
    <link rel="icon" href="{{ asset('img/LogoPusat.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('img/LogoPusat.png') }}" type="image/x-icon">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
    
    /* Custom Scrollbar for better mobile UX */
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">
    @yield('content')

    <!-- Global Toast Notification -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: 'Alhamdulillah!',
                html: {!! json_encode(session('success')) !!}
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: 'Afwan!',
                html: {!! json_encode(session('error')) !!}
            });
        @endif

        @if($errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Afwan!',
                html: '<div class="text-left text-xs"><ul>@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul></div>'
            });
        @endif
    </script>
</body>
</html>