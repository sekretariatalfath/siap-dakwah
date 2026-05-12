<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- GANTI TITLE DI SINI --}}
    <title>SIAP Dakwah | LDK Al-Fath Telkom University</title>
    
    {{-- SEO META TAGS --}}
    <meta name="google-site-verification" content="nXqaFPnYRKukZbpr4a_E_jLWwoWYgpVTLcw2sQaMrhM" />
    <meta name="description" content="Sistem Informasi Administrasi Pejuang (SIAP) Dakwah - Pusat kendali operasional dan layanan administrasi satu pintu LDK Al-Fath Telkom University.">
    <meta name="keywords" content="SIAP Dakwah, LDK Al-Fath, Telkom University, Administrasi Dakwah, Sekretariat Al-Fath, Pejuang Dakwah">
    <meta name="author" content="Biro Kesekretariatan LDK Al-Fath">
    
    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://siapdakwah.vercel.app/">
    <meta property="og:title" content="SIAP Dakwah | LDK Al-Fath">
    <meta property="og:description" content="Tertib Administrasi, Dakwah Berprestasi. Layanan administrasi digital LDK Al-Fath.">
    <meta property="og:image" content="{{ asset('img/LogoPusat.png') }}">

    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://siapdakwah.vercel.app/">
    <meta property="twitter:title" content="SIAP Dakwah | LDK Al-Fath">
    <meta property="twitter:description" content="Tertib Administrasi, Dakwah Berprestasi. Layanan administrasi digital LDK Al-Fath.">
    <meta property="twitter:image" content="{{ asset('img/LogoPusat.png') }}">

    {{-- FAVICON --}}
    <link rel="icon" href="{{ asset('img/LogoPusat.png') }}?v=2" type="image/png">
    <link rel="shortcut icon" href="{{ asset('img/LogoPusat.png') }}?v=2" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/LogoPusat.png') }}?v=2">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animasi-kotak {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }

    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
    .delay-400 { animation-delay: 400ms; }
    
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