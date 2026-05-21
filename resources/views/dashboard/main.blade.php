@extends('layouts.dashboard')

@section('dashboard-content')
        {{-- Header & Filter --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight leading-none">
                    Ahlan wa Sahlan, <span class="{{ $theme['text'] }}">{{ Auth::user()->name }}</span> 👋
                </h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Laporan ringkas administrasi <span class="font-bold text-gray-800">{{ $unitName }}</span> saat ini.</p>
            </div>

            @if($isKestari)
            <div class="relative group">
                {{-- Kontainer Dropdown yang Lebih Estetik --}}
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100 hover:border-gray-300 transition-all duration-300">
                    <span class="text-[10px] font-black uppercase text-gray-700 tracking-widest border-r border-gray-100 pr-3">Filter Unit</span>
                    
                    <form action="{{ route('dashboard') }}" method="GET" class="flex items-center">
                        <select name="unit_filter" onchange="this.form.submit()" 
                            class="text-xs font-black bg-transparent border-none focus:ring-0 cursor-pointer text-gray-700 uppercase tracking-tighter p-0 pr-8">
                            <option value="" class="font-bold">Seluruh Unit (Global)</option>
                            
                            @foreach($allUnits as $unit)
                                @php
                                    // Penyesuaian Nama agar Konsisten dan Rapi
                                    $displayName = $unit;
                                    $displayName = str_replace('LDF Al-Fath Fakultas ', 'F. ', $displayName);
                                    $displayName = str_replace('Departemen ', 'Dep. ', $displayName);
                                @endphp
                                <option value="{{ $unit }}" {{ request('unit_filter') == $unit ? 'selected' : '' }} class="font-semibold py-2">
                                    {{ $displayName }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- 4. STATISTIK --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            {{-- Kartu Surat Masuk --}}
            <div class="bg-white rounded-[24px] md:rounded-[32px] shadow-sm border border-gray-100 p-5 md:p-8 flex flex-col justify-between hover:shadow-md transition-all animasi-kotak">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-700 text-[10px] font-black uppercase tracking-widest">Surat Masuk</p>
                        <h2 class="text-3xl md:text-4xl font-black text-gray-900 mt-1">{{ $totalMasuk }}</h2>
                    </div>
                    <div class="{{ $theme['light'] }} p-4 rounded-2xl {{ $theme['text'] }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div class="mt-6">
                    <span class="px-3 py-1.5 rounded-xl {{ $perluVerifikasi > 0 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-green-50 text-green-700 border-green-200' }} text-[9px] font-black uppercase border tracking-tighter">
                        {{ $perluVerifikasi }} Perlu Verifikasi 
                    </span>
                </div>
            </div>

            {{-- Kartu Berita Acara --}}
            <div class="bg-white rounded-[24px] md:rounded-[32px] shadow-sm border border-gray-100 p-5 md:p-8 flex flex-col justify-between hover:shadow-md transition-all animasi-kotak delay-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-700 text-[10px] font-black uppercase tracking-widest">Berita Acara</p>
                        <h2 class="text-3xl md:text-4xl font-black text-indigo-700 mt-1">{{ $totalBA }}</h2>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div class="mt-6">
                    <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest opacity-70">Sinkronisasi Data</span>
                </div>
            </div>

            {{-- Kartu Surat Keluar --}}
            <div class="bg-white rounded-[24px] md:rounded-[32px] shadow-sm border border-gray-100 p-5 md:p-8 flex flex-col justify-between hover:shadow-md transition-all animasi-kotak delay-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-700 text-[10px] font-black uppercase tracking-widest">Surat Keluar</p>
                        <h2 class="text-3xl md:text-4xl font-black text-yellow-600 mt-1">{{ $totalKeluar }}</h2>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-2xl text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap gap-1">
                    @foreach($detailJenis as $jenis => $jml)
                        <span class="text-[8px] font-black px-2 py-0.5 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-100 uppercase">{{ $jenis }}: {{ $jml }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 5. AKSES CEPAT --}}
        <div class="mb-12">
            <h3 class="text-xs font-black text-gray-700 uppercase tracking-[3px] mb-6 px-2">Database & Berkas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Template --}}
                <a href="{{ route('resource.index', 'template') }}" class="flex items-center gap-4 md:gap-5 bg-white p-5 md:p-6 rounded-[24px] md:rounded-[32px] border border-gray-100 hover:border-red-200 transition group shadow-sm animasi-kotak delay-100">
                    <div class="bg-red-50 p-4 rounded-2xl text-red-600 group-hover:bg-red-600 group-hover:text-white transition shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-gray-900 uppercase">Template Berkas</h4>
                        <p class="text-[10px] text-gray-700 font-bold uppercase mt-0.5 tracking-tighter">Format Dokumen</p>
                    </div>
                </a>
                
                {{-- Pedoman --}}
                <a href="{{ route('resource.index', 'pedoman') }}" class="flex items-center gap-4 md:gap-5 bg-white p-5 md:p-6 rounded-[24px] md:rounded-[32px] border border-gray-100 hover:border-indigo-200 transition group shadow-sm animasi-kotak delay-200">
                    <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-gray-900 uppercase">Pedoman & SOP</h4>
                        <p class="text-[10px] text-gray-700 font-bold uppercase mt-0.5 tracking-tighter">Panduan Administrasi</p>
                    </div>
                </a>
                
                {{-- Informasi --}}
                <a href="{{ route('resource.index', 'informasi') }}" class="flex items-center gap-4 md:gap-5 bg-white p-5 md:p-6 rounded-[24px] md:rounded-[32px] border border-gray-100 hover:border-emerald-200 transition group shadow-sm animasi-kotak delay-300">
                    <div class="bg-emerald-50 p-4 rounded-2xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-gray-900 uppercase">Pusat Informasi</h4>
                        <p class="text-[10px] text-gray-700 font-bold uppercase mt-0.5 tracking-tighter">Berita & Pengumuman</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- 6. KALENDER KEGIATAN --}}
        <div class="mb-10">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-5 px-2">
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[3px]">Kalender Kegiatan</h3>
                </div>
                @if($isKestari)
                    <button onclick="document.getElementById('formKalender').classList.toggle('hidden')" class="text-[9px] font-bold text-gray-700 bg-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition">
                        Sesuaikan Link
                    </button>
                @endif
            </div>

            {{-- Form Edit --}}
            @if($isKestari)
            <form id="formKalender" action="{{ route('dashboard.calendar.update') }}" method="POST" class="hidden mb-6 bg-white p-6 rounded-[32px] border-2 border-dashed border-gray-200 animate-in fade-in slide-in-from-top-4 duration-300">
                @csrf
                <div class="flex flex-col md:flex-row gap-3">
                    <input type="url" name="calendar_link" value="{{ $calendarLink }}" required class="flex-1 bg-gray-50 border-none rounded-xl p-3 text-xs focus:ring-2 focus:ring-gray-900 shadow-inner outline-none" placeholder="Masukkan Link Spreadsheet (PubHTML)">
                    <button type="submit" class="bg-gray-900 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-black transition-all">
                        Simpan
                    </button>
                </div>
            </form>
            @endif

            <div class="space-y-4">
                {{-- Banner Card (Ukuran dikecilkan dari p-12 ke p-8) --}}
                <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-black p-6 md:p-8 rounded-[24px] md:rounded-[32px] shadow-xl flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden group animasi-kotak delay-200">
                    {{-- Efek cahaya --}}
                    <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -mr-24 -mt-24 blur-3xl group-hover:bg-white/10 transition-all duration-700"></div>
                    
                    <div class="text-center md:text-left z-10">
                        <h4 class="text-white font-bold text-xl md:text-2xl tracking-tight leading-tight">Akses Spreadsheet</h4>
                    </div>
                    
                    <a href="{{ $calendarLink }}" target="_blank" class="bg-white text-gray-900 px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase hover:scale-105 active:scale-95 transition-all shadow-white/10 shadow-2xl z-10 tracking-widest">
                        Buka Kalender
                    </a>
                </div>

                {{-- Preview Iframe (Radius disamakan dengan kartu stat) --}}
                <div class="bg-white rounded-[32px] p-2 border border-gray-100 shadow-sm overflow-hidden h-[550px] hidden md:block group animasi-kotak delay-300">
                    <iframe src="{{ $calendarLink }}" class="w-full h-full rounded-[24px] border-0 opacity-90 group-hover:opacity-100 transition-opacity" loading="lazy"></iframe>
                </div>
            </div>
        </div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    [x-cloak] { display: none !important; }
</style>

@endsection