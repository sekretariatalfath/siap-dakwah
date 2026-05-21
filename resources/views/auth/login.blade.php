@extends('layout')

@section('content')
<style>
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
</style>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100 animasi-kotak">
        
        {{-- HEADER BARU: SIAP DAKWAH --}}
        <div class="text-center mb-8">
            {{-- Kalau ada logo file, uncomment baris bawah ini --}}
            <img src="{{ asset('img/LogoPusat.png') }}" class="h-32 mx-auto mb-4">

            <h1 class="text-4xl font-extrabold text-red-700 tracking-tight">SIAP Dakwah</h1>
            <p class="text-gray-500 font-medium mt-2 text-sm">
                Sistem Informasi Administrasi <span class="text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded">Pejuang Dakwah</span>
            </p>
            <p class="text-[10px] text-gray-600 mt-2 uppercase tracking-widest font-bold">LDK Al-Fath Telkom University</p>
        </div>

        {{-- ALERT ERROR --}}
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm border-l-4 border-red-500 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-gray-700 text-xs font-bold uppercase mb-2 tracking-wide">Email Pengurus</label>
                <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:outline-none transition shadow-sm placeholder-gray-400" placeholder="nama.unit@alfathunitel.org" required>
            </div>
            
            <div>
                <label class="block text-gray-700 text-xs font-bold uppercase mb-2 tracking-wide">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:outline-none transition shadow-sm placeholder-gray-400" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-red-800 transition transform hover:scale-[1.02] shadow-lg shadow-red-200 flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                Masuk Sistem
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <a href="/" class="text-xs text-gray-600 hover:text-red-600 transition font-medium flex items-center justify-center gap-1 group">
                <span class="group-hover:-translate-x-1 transition">←</span> Kembali ke Halaman Depan
            </a>
        </div>
    </div>
    
    {{-- Footer Kecil --}}
    <div class="fixed bottom-4 text-center w-full text-[10px] text-gray-300 pointer-events-none">
        &copy; {{ date('Y') }} SIAP Dakwah - LDK Al-Fath
    </div>
</div>
@endsection