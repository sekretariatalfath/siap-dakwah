@extends('layout')

@section('content')
<style>
    /* Force lock horizontal scroll and ghost space */
    html, body {
        max-width: 100%;
        overflow-x: hidden;
        position: relative;
        background-color: #111827; /* Gray-900: Match footer color */
        margin: 0;
        padding: 0;
    }
    .ghost-fix {
        max-width: 100vw;
    }

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
</style>

<div class="min-h-screen flex flex-col bg-white font-inter selection:bg-red-100 selection:text-red-900 ghost-fix">
    <div class="relative w-full overflow-x-clip flex-grow flex flex-col"> {{-- Super-Clip Wrapper --}}
    <div class="flex-grow">
    
    {{-- GRADIENT ORNAMENT --}}
    <div class="hidden md:block absolute top-0 right-0 -z-10 w-[600px] h-[600px] bg-red-100/30 blur-[120px] rounded-full translate-x-1/3 -translate-y-1/3"></div>
    <div class="hidden md:block absolute bottom-0 left-0 -z-10 w-[400px] h-[400px] bg-red-50/50 blur-[100px] rounded-full -translate-x-1/3 translate-y-1/3"></div>

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="container mx-auto px-4 h-16 md:h-20 flex justify-between items-center gap-2">
            {{-- Logo --}}
            <div class="flex items-center gap-2 md:gap-3">
                <div class="w-8 h-7 md:w-12 md:h-10 bg-red-700 rounded-lg md:rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-red-200 text-[9px] md:text-base">
                    LDK
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-gray-900 text-sm md:text-xl leading-none tracking-tight">SIAP Dakwah</span>
                    <span class="text-[6px] md:text-[8px] text-red-600 font-black uppercase tracking-[0.2em] mt-0.5 md:mt-1">LDK Al-Fath Telkom University</span>
                </div>
            </div>

            {{-- CTA --}}
            <div class="flex items-center">
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-900 text-white px-4 md:px-6 py-2 md:py-2.5 rounded-full text-[10px] md:text-sm font-bold hover:bg-black transition-all shadow-xl shadow-gray-200 flex items-center gap-1.5 md:gap-2 group">
                        <span class="hidden md:inline">Dashboard</span>
                        <span class="md:hidden">Panel</span>
                        <svg class="w-3 h-3 md:w-4 md:h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="text-red-700 bg-red-50 hover:bg-red-700 hover:text-white px-5 md:px-6 py-1.5 md:py-2.5 rounded-full text-[10px] md:text-sm font-black transition-all border border-red-100 flex items-center gap-2 active:scale-95 shadow-sm">
                        <span class="hidden md:inline">Login Pengurus</span>
                        <span class="md:hidden uppercase tracking-widest">Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <header class="relative pt-12 md:pt-16 pb-20 md:pb-24 overflow-hidden w-full">
        <div class="container mx-auto px-4 md:px-6 text-center">
            
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 border border-red-100 text-red-700 text-[11px] font-black uppercase tracking-widest mb-10 animasi-kotak">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                </span>
                Integrated Digital Administration
            </div>
 
            <h1 class="text-3xl md:text-7xl font-black text-gray-900 mb-6 md:mb-8 leading-[1.1] tracking-tight animasi-kotak delay-100 px-2">
                Tertib Administrasi, <br class="hidden md:block"> 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-800 to-red-500">
                    Dakwah Berprestasi
                </span>
            </h1>
            
            <p class="text-sm md:text-xl text-gray-500 mb-10 md:mb-14 max-w-3xl mx-auto leading-relaxed font-medium animasi-kotak delay-200">
                Sistem Informasi Administrasi Pejuang (SIAP) Dakwah adalah pusat kendali operasional 
                dan layanan administrasi satu pintu untuk mendukung efektivitas gerakan dakwah LDK Al-Fath.
            </p>
            
            {{-- CTAs --}}
            <div class="flex flex-col md:flex-row justify-center gap-4 md:gap-6 animasi-kotak delay-300">
                <a href="{{ route('public.template') }}" class="min-w-[220px] px-6 md:px-8 py-3 md:py-4 bg-red-700 text-white font-black rounded-xl md:rounded-2xl shadow-xl shadow-red-200 hover:bg-red-800 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 tracking-wide text-[11px] md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    E-Library Template
                </a>

                <a href="{{ route('public.sop') }}" class="min-w-[220px] px-6 md:px-8 py-3 md:py-4 bg-white text-gray-700 font-black rounded-xl md:rounded-2xl shadow-sm border border-gray-200 hover:border-red-600 hover:text-red-700 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 tracking-wide text-[11px] md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Pusat Panduan & SOP
                </a>

                <a href="{{ route('public.informasi') }}" class="min-w-[220px] px-6 md:px-8 py-3 md:py-4 bg-white text-gray-700 font-black rounded-xl md:rounded-2xl shadow-sm border border-gray-200 hover:border-red-600 hover:text-red-700 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 tracking-wide text-[11px] md:text-base">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Umum
                </a>
            </div>
        </div>
    </header>

    {{-- BRIEF SECTION (SIMPLIFIED & RELAXED) --}}
    <section class="py-16 md:py-24 bg-red-50/50 border-y border-red-100/50 w-full overflow-hidden">
        <div class="container mx-auto px-5 md:px-6 max-w-4xl text-center animasi-kotak">
            <h2 class="text-[10px] font-black text-red-600 uppercase tracking-[0.3em] mb-4">Visi SIAP Dakwah</h2>
            <h3 class="text-2xl md:text-4xl font-black text-gray-900 mb-6 tracking-tight uppercase leading-tight">Mewujudkan Ekosistem Dakwah yang Profesional & Terintegrasi</h3>
            <p class="text-gray-600 leading-relaxed font-medium text-sm md:text-lg">
                Administrasi bukan sekadar tumpukan kertas, melainkan bukti keseriusan kita dalam mengelola amanah dakwah. SIAP Dakwah hadir sebagai solusi digital untuk menyederhanakan alur birokrasi, memastikan data tersimpan rapi, dan memudahkan setiap pejuang dakwah dalam mengakses kebutuhan administratif secara mandiri.
            </p>
            
            <div class="flex flex-col md:flex-row justify-center gap-6 md:gap-12 mt-10 md:mt-12 animasi-kotak delay-100">
                <div class="flex items-center gap-4">
                    <div class="w-2 h-8 bg-red-700 rounded-full"></div>
                    <div class="text-left">
                        <h4 class="font-black text-gray-900 text-sm uppercase tracking-wider">Otomasi</h4>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-0.5">Surat & Berkas Instan</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-2 h-8 bg-red-700 rounded-full"></div>
                    <div class="text-left">
                        <h4 class="font-black text-gray-900 text-sm uppercase tracking-wider">Sentralisasi</h4>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-0.5">Satu Pintu Layanan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- LAYANAN SECTION (GRAND MODULES) --}}
    <section id="layanan" class="py-20 md:py-32 w-full overflow-hidden">
        <div class="container mx-auto px-5 md:px-6 text-center">
            <div class="mb-12 md:mb-20">
                <h2 class="text-2xl md:text-4xl font-black text-gray-900 mb-3 md:mb-4 tracking-tight uppercase">Modul Administrasi</h2>
                <p class="text-gray-400 font-bold text-[9px] md:text-sm uppercase tracking-widest px-4">Layanan digital komprehensif bagi pengurus</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 md:gap-10">
                
                {{-- Modul 1 --}}
                <div class="group p-6 md:p-10 rounded-[32px] md:rounded-[40px] bg-white border border-gray-100 hover:border-red-100 hover:shadow-2xl hover:shadow-red-100/50 transition-all duration-500 text-left relative animasi-kotak">
                    <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 group-hover:bg-red-700 group-hover:text-white transition-all duration-500">
                        📄
                    </div>
                    <h3 class="font-black text-xl md:text-2xl mb-3 md:mb-4 text-gray-900 uppercase">Bank Template</h3>
                    <p class="text-gray-500 leading-relaxed font-medium mb-6 md:mb-8 text-xs md:text-base">
                        Gunakan standar berkas resmi Al-Fath untuk mempermudah pembuatan surat, proposal, hingga laporan pertanggungjawaban (LPJ).
                    </p>
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="w-0 group-hover:w-full h-full bg-red-600 transition-all duration-700"></div>
                    </div>
                </div>

                {{-- Modul 2 --}}
                <div class="group p-6 md:p-10 rounded-[32px] md:rounded-[40px] bg-white border border-gray-100 hover:border-orange-100 hover:shadow-2xl hover:shadow-orange-100/50 transition-all duration-500 text-left relative animasi-kotak delay-100">
                    <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                        ⚖️
                    </div>
                    <h3 class="font-black text-xl md:text-2xl mb-3 md:mb-4 text-gray-900 uppercase">Panduan & SOP</h3>
                    <p class="text-gray-500 leading-relaxed font-medium mb-6 md:mb-8 text-xs md:text-base">
                        Akses aturan organisasi, alur peminjaman inventaris, hingga mekanisme pengajuan dana kegiatan secara transparan.
                    </p>
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="w-0 group-hover:w-full h-full bg-orange-500 transition-all duration-700"></div>
                    </div>
                </div>

                {{-- Modul 3 --}}
                <div class="group p-6 md:p-10 rounded-[32px] md:rounded-[40px] bg-white border border-gray-100 hover:border-indigo-100 hover:shadow-2xl hover:shadow-indigo-100/50 transition-all duration-500 text-left relative animasi-kotak delay-200">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 group-hover:bg-indigo-700 group-hover:text-white transition-all duration-500">
                        💻
                    </div>
                    <h3 class="font-black text-xl md:text-2xl mb-3 md:mb-4 text-gray-900 uppercase">Control Center</h3>
                    <p class="text-gray-500 leading-relaxed font-medium mb-6 md:mb-8 text-xs md:text-base">
                        Dashboard khusus pengurus untuk mengelola nomor surat otomatis, absensi digital, dan pengarsipan data secara *real-time*.
                    </p>
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="w-0 group-hover:w-full h-full bg-indigo-600 transition-all duration-700"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>

    {{-- FOOTER (GRAND DARK FOOTER) --}}
    <footer class="bg-gray-900 py-16 md:py-24 text-white relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-10 md:gap-12 border-b border-white/10 pb-10 md:pb-12 mb-10 md:mb-12 text-center md:text-left">
                <div>
                    <h4 class="font-black text-3xl md:text-4xl tracking-tight mb-2 text-red-600 uppercase">SIAP Dakwah</h4>
                    <p class="text-gray-400 text-[10px] md:text-sm max-w-sm font-medium mx-auto md:mx-0">Sistem Informasi Administrasi Pejuang Dakwah <br> LDK Al-Fath Universitas Telkom.</p>
                </div>
                
                <div class="flex flex-wrap justify-center gap-6 md:gap-10 items-center uppercase text-[9px] md:text-[11px] font-black tracking-[0.3em] md:tracking-[0.4em] text-gray-400">
                    <a href="https://www.instagram.com/alfathtelu" target="_blank" class="hover:text-red-500 transition">Instagram</a>
                    <a href="{{ route('login') }}" class="hover:text-red-500 transition">Portal Admin</a>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] text-gray-500 font-bold uppercase tracking-[0.3em]">
                <p>&copy; {{ date('Y') }} Biro Kesekretariatan LDK Al-Fath. All Rights Reserved.</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    System Online
                </div>
            </div>
        </div>
    </footer>
    </div> {{-- End Super-Clip Wrapper --}}
</div>
@endsection