<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil Absen - SIAP Dakwah</title>
    {{-- Favicon Logo Al-Fath --}}
    <link rel="icon" href="{{ asset('img/LogoPusat.png') }}" type="image/x-icon">
    {{-- Load Tailwind & Inter Font --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f8f9fa] flex items-center justify-center min-h-screen p-4">
    
    <div class="bg-white rounded-3xl shadow-2xl p-10 w-full max-w-md border-t-[10px] border-red-800 text-center relative overflow-hidden">
        
        {{-- Dekorasi Latar Belakang --}}
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-50 rounded-full opacity-50"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-emerald-50 rounded-full opacity-50"></div>
        <div class="mb-8 p-6 bg-green-50/50 rounded-3xl border border-green-100/50">
            <h2 class="text-xl font-extrabold text-green-900 uppercase tracking-tight">
                {{ $sesi->nama_kegiatan ?? 'Kegiatan Al-Fath' }}
            </h2>
            <p class="text-[10px] font-bold text-green-800/60 uppercase tracking-[0.2em] mt-1">
                {{ $sesi->kategori ?? 'Presensi Kehadiran' }}
            </p>
        </div>

        {{-- Ikon Centang Beranimasi --}}
        <div class="mb-8 flex justify-center relative">
            <div class="bg-emerald-50 p-6 rounded-full animate-bounce">
                <svg class="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        {{-- Judul --}}
        <h1 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tight">Berhasil!</h1>
        <div class="h-1.5 w-12 bg-emerald-500 mx-auto mb-8 rounded-full"></div>

        {{-- Pesan Utama --}}
        <div class="space-y-4 mb-10 relative">
            <p class="text-gray-600 font-semibold leading-relaxed">
                Syukron katsiran, kehadiranmu telah tercatat di sistem 
                <span class="text-red-800 font-bold uppercase italic">SIAP Dakwah</span>.
            </p>
            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                <p class="text-[11px] text-gray-400 italic font-medium leading-relaxed">
                    "Barangsiapa yang memudahkan urusan saudaranya, Allah akan memudahkan urusannya di dunia dan akhirat."
                </p>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col gap-3 relative">
            {{-- Tombol Edit (Pakai variabel idSesi yang kita kirim dari Controller) --}}
            <a href="{{ route('presensi.public', $idSesi) }}?submission_id={{ $submissionId }}" 
               class="w-full bg-white border-2 border-red-800 text-red-800 font-black py-4 rounded-2xl hover:bg-red-50 transition-all flex items-center justify-center gap-3 text-xs uppercase tracking-widest active:scale-95 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Jawaban Sebelumnya
            </a>

            <p class="text-[9px] text-gray-300 mt-4 uppercase font-extrabold tracking-[0.3em]">
                LDK AL-FATH TELKOM UNIVERSITY
            </p>
        </div>
    </div>

</body>
</html>