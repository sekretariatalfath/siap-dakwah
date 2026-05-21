@extends('layout')

@section('content')
<div class="min-h-screen flex bg-gray-50 relative overflow-x-hidden">
    
    {{-- Mobile Overlay Backdrop --}}
    <div id="mobile-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-900/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="w-80 text-white {{ $theme['bg'] }} flex flex-col shadow-2xl fixed md:sticky top-0 left-0 h-screen overflow-y-auto z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
        
        {{-- Close Button Mobile --}}
        <button onclick="toggleSidebar()" class="md:hidden absolute top-6 right-6 text-white/70 hover:text-white transition bg-black/10 rounded-full p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="p-8 flex flex-col items-center border-b border-white/20 mt-4 md:mt-0">
            <div class="bg-white p-3 rounded-full mb-4 shadow-lg overflow-hidden flex items-center justify-center w-32 h-32">
                <img src="{{ asset('img/' . ($logoFile ?? 'LogoPusat.png')) }}" alt="Logo Unit" class="max-h-full max-w-full object-contain">
            </div>
            
            <h2 class="font-bold text-lg leading-tight text-center px-2">{{ Auth::user()->unit }}</h2>
            <span class="text-[10px] opacity-75 uppercase tracking-[2px] mt-2 font-bold">{{ Auth::user()->role }}</span>
        </div>
        
        <nav class="flex-1 p-6 space-y-2">
            {{-- 1. DASHBOARD --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-white/20 border-l-4 border-white' : 'hover:bg-black/10' }} rounded-xl transition font-bold text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>

            {{-- 2. DROPDOWN SURAT MASUK --}}
            <div x-data="{ open: {{ request()->routeIs('surat-masuk.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-black/10 rounded-xl transition text-sm font-bold">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <span>Surat Masuk</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="mt-1 ml-9 space-y-1 text-xs border-l border-white/20 pl-4">
                    <a href="{{ route('surat-masuk.index') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('surat-masuk.index') ? 'text-white font-bold underline' : '' }}">Arsip Surat Masuk</a>
                </div>
            </div>

            {{-- 3. DROPDOWN SURAT KELUAR --}}
            <div x-data="{ open: {{ request()->routeIs('surat.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-black/10 rounded-xl transition text-sm font-bold">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        <span>Surat Keluar</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="mt-1 ml-9 space-y-1 text-xs border-l border-white/20 pl-4">
                    <a href="{{ route('surat.create') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('surat.create') ? 'text-white font-bold underline' : '' }}">Buat Nomor Surat</a>
                    <a href="{{ route('surat.arsip') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('surat.arsip') ? 'text-white font-bold underline' : '' }}">Arsip Surat Keluar</a>
                </div>
            </div>

            {{-- 4. DROPDOWN BERITA ACARA --}}
            <div x-data="{ open: {{ request()->routeIs('berita-acara.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-black/10 rounded-xl transition text-sm font-bold">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Berita Acara</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="mt-1 ml-9 space-y-1 text-xs border-l border-white/20 pl-4">
                    <a href="{{ route('berita-acara.create') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('berita-acara.create') ? 'text-white font-bold underline' : '' }}">Buat Berita Acara</a>
                </div>
            </div>

            {{-- 5. DROPDOWN PRESENSI KADER --}}
            <div x-data="{ open: {{ request()->routeIs('presensi.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-black/10 rounded-xl transition text-sm font-bold">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <span>Presensi Kader</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="mt-1 ml-9 space-y-1 text-xs border-l border-white/20 pl-4">
                    <a href="{{ route('presensi.index') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('presensi.index') ? 'text-white font-bold underline' : '' }}">Data Kehadiran</a>
                </div>
            </div>

            {{-- 6. DROPDOWN NOTULENSI --}}
            <div x-data="{ open: {{ request()->routeIs('notulensi.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-black/10 rounded-xl transition text-sm font-bold">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Notulensi</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="mt-1 ml-9 space-y-1 text-xs border-l border-white/20 pl-4">
                    <a href="{{ route('notulensi.create') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('notulensi.create') ? 'text-white font-bold underline' : '' }}">Generate Notulensi</a>
                    <a href="{{ route('notulensi.index') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('notulensi.index') ? 'text-white font-bold underline' : '' }}">Arsip Notulensi</a>
                </div>
            </div>

            {{-- 7. DROPDOWN MANAJEMEN PROPOSAL --}}
            <div x-data="{ open: {{ request()->routeIs('proposal.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-black/10 rounded-xl transition text-sm font-bold">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>Proposal</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="mt-1 ml-9 space-y-1 text-xs border-l border-white/20 pl-4">
                    <a href="{{ route('proposal.index') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('proposal.index') ? 'text-white font-bold underline' : '' }}">Arsip & Input Proposal</a>
                </div>
            </div>

            {{-- 8. DROPDOWN LPJ & EVALUASI --}}
            <div x-data="{ open: {{ request()->routeIs('evaluasi.*') || request()->routeIs('lpj.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 hover:bg-black/10 rounded-xl transition text-sm font-bold">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>LPJ & Evaluasi</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="mt-1 ml-9 space-y-1 text-xs border-l border-white/20 pl-4">
                    <a href="{{ route('evaluasi.generate.form') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('evaluasi.generate.form') ? 'text-white font-bold underline' : '' }}">Generate Evaluasi</a>
                    <a href="{{ route('evaluasi.index') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('evaluasi.index') ? 'text-white font-bold underline' : '' }}">Arsip Evaluasi</a>
                    <a href="{{ route('lpj.index') }}" class="block py-2 opacity-80 hover:text-white transition italic {{ request()->routeIs('lpj.index') ? 'text-white font-bold underline' : '' }}">Input LPJ</a>
                </div>
            </div>
        </nav>

        <div class="p-6 border-t border-white/20 space-y-3 mt-auto">
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('settings.*') ? 'bg-white/20 border-l-4 border-white' : 'hover:bg-white/10' }} rounded-xl transition font-bold text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Pengaturan Akun
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full text-center px-4 py-3 bg-white/10 text-white font-bold rounded-xl transition hover:bg-white/20 text-sm">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full max-w-full px-4 py-8 md:px-8 overflow-y-auto h-screen bg-gray-50 overflow-x-hidden">
        {{-- Mobile Hamburger --}}
        <div class="md:hidden mb-6 flex justify-between items-center bg-white p-3 md:p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 md:gap-3">
                <img src="{{ asset('img/' . ($logoFile ?? 'LogoPusat.png')) }}" class="h-8 w-8 md:h-9 md:w-9 object-contain bg-gray-50 p-1 rounded-full border border-gray-100">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-800 text-xs md:text-sm leading-tight">{{ $unitName }}</span>
                    <span class="text-[8px] md:text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ Auth::user()->role }}</span>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="p-2 md:p-2.5 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors active:scale-95">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>

        @yield('dashboard-content')

        {{-- Footer Kontak Person --}}
        <footer class="mt-12 pt-6 pb-2 border-t border-gray-200/60 flex flex-col items-center justify-center">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest text-center mb-4">Pusat Bantuan & Kendala Sistem</p>
            
            <div class="flex flex-col md:flex-row gap-6 md:gap-12 w-full max-w-4xl justify-center px-4">
                {{-- CPs Pusat --}}
                @if(isset($contactPersons['PUSAT']) && count($contactPersons['PUSAT']) > 0)
                <div class="flex flex-col items-center">
                    <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full mb-3 tracking-widest uppercase">Pusat</span>
                    <div class="flex flex-wrap justify-center gap-3">
                        @foreach($contactPersons['PUSAT'] as $cp)
                        <a href="https://wa.me/{{ $cp['wa'] }}" target="_blank" class="hover:text-indigo-700 transition-colors flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100 text-xs font-bold text-gray-700 hover:border-indigo-200 hover:shadow-md">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $cp['nama'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CPs Fakultas --}}
                @if(isset($contactPersons['FAKULTAS']) && count($contactPersons['FAKULTAS']) > 0)
                <div class="flex flex-col items-center">
                    <span class="text-[9px] font-black text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full mb-3 tracking-widest uppercase">Fakultas</span>
                    <div class="flex flex-wrap justify-center gap-3">
                        @foreach($contactPersons['FAKULTAS'] as $cp)
                        <a href="https://wa.me/{{ $cp['wa'] }}" target="_blank" class="hover:text-emerald-700 transition-colors flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100 text-xs font-bold text-gray-700 hover:border-emerald-200 hover:shadow-md">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $cp['nama'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <p class="text-[9px] text-gray-400 font-bold tracking-[0.2em] uppercase mt-8">SIAP DAKWAH &copy; {{ date('Y') }} • BIRO KESEKRETARIATAN LDK AL-FATH</p>
        </footer>
    </main>

</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        
        // Toggle slide
        sidebar.classList.toggle('-translate-x-full');
        
        // Toggle backdrop fade
        if (overlay.classList.contains('hidden')) {
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        } else {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300); // Wait for transition
        }
    }
</script>
@endsection
