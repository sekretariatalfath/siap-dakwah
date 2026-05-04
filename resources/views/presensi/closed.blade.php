<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Ditutup - SIAP Dakwah</title>
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('img/LogoPusat.png') }}" type="image/x-icon">
    {{-- Load Tailwind & Inter Font --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#fcfcfc] flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-3xl shadow-2xl p-10 w-full max-w-md border-t-[10px] border-gray-400 text-center relative overflow-hidden">
        
        {{-- Dekorasi Latar Belakang --}}
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-gray-50 rounded-full opacity-50"></div>

        <div class="mb-8 p-6 bg-gray-50 rounded-3xl border border-gray-100">
            <h2 class="{{ isset($sesi) && strlen($sesi->nama_kegiatan) > 30 ? 'text-sm' : 'text-xl' }} font-black text-gray-500 uppercase tracking-tight italic line-through leading-tight">
                {{ $sesi->nama_kegiatan ?? 'SESI PRESENSI' }}
            </h2>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">
                {{ $sesi->kategori ?? 'Akses Ditutup' }}
            </p>
        </div>

        {{-- Ikon Gembok / Jam --}}
        <div class="mb-8 flex justify-center relative">
            <div class="bg-gray-100 p-6 rounded-full">
                <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
        </div>

        {{-- Judul --}}
        <h1 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tight">Akses Ditutup</h1>
        <div class="h-1.5 w-12 bg-gray-300 mx-auto mb-8 rounded-full"></div>

        {{-- Pesan Utama --}}
        <div class="space-y-4 mb-10 relative">
            <p class="text-gray-600 font-semibold leading-relaxed">
                Mohon maaf, tautan presensi untuk kegiatan ini sudah 
                <span class="text-red-800 font-bold uppercase italic">Tidak Aktif</span> 
                atau telah ditutup oleh pengelola.
            </p>
            <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                <p class="text-[11px] text-red-800 italic font-medium leading-relaxed">
                    "Waktu adalah pedang, jika kita tidak menggunakannya untuk kebaikan, ia akan memotong kita."
                </p>
            </div>
        </div>

        {{-- Instruksi Tambahan --}}
        <div class="flex flex-col gap-3 relative">
            <div class="text-[11px] text-gray-400 font-medium space-y-1">
                <p>Silakan hubungi <b>Pengelola yang Bersangkutan</b></p>
                <p>unit terkait jika hal ini merupakan sebuah kekeliruan.</p>
            </div>

            {{-- Footer Branding --}}
            <p class="text-[9px] text-gray-300 mt-8 uppercase font-extrabold tracking-[0.3em]">
                LDK AL-FATH TELKOM UNIVERSITY
            </p>
        </div>
    </div>

</body>
</html>