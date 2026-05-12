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

    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
</style>
<div class="min-h-screen bg-gray-50/30 font-inter selection:bg-red-100 selection:text-red-700">
    
    {{-- NAVBAR --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="container mx-auto px-6 h-16 flex justify-between items-center flex-row-reverse">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:inline">Pusat Informasi Terintegrasi</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest md:hidden">Informasi</span>
            </div>
            
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center text-white font-bold group-hover:bg-red-700 transition-all shadow-lg shadow-gray-200 group-hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                <span class="font-black text-gray-900 text-xs group-hover:text-red-700 transition uppercase tracking-widest hidden md:inline">Kembali ke Beranda</span>
                <span class="font-black text-gray-900 text-[10px] group-hover:text-red-700 transition uppercase tracking-widest md:hidden">Kembali</span>
            </a>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <header class="relative bg-red-700 pt-20 pb-32 overflow-hidden">
        {{-- Background Ornament --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white text-[9px] font-black uppercase tracking-widest mb-6 backdrop-blur-md">
                Pusat Informasi
            </div>
            <h1 class="text-2xl md:text-5xl font-black text-white mb-4 uppercase tracking-tighter leading-none animate-in fade-in slide-in-from-bottom-4 duration-700">
                Informasi Umum
            </h1>
            <p class="text-red-100 max-w-xl mx-auto text-[10px] md:text-sm font-medium leading-relaxed opacity-80 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-100">
                Akses cepat ke berbagai informasi penting, pengumuman, dan database untuk mendukung aktivitas dakwah Mas/Mbak.
            </p>
        </div>
    </header>

    <main class="container mx-auto px-6 py-12 -mt-16 relative z-20">
        
        {{-- SEARCH & TOOLS --}}
        <div class="max-w-4xl mx-auto mb-10 animasi-kotak">
            <div class="bg-white p-2 rounded-[32px] shadow-2xl shadow-gray-200/40 border border-gray-100 flex flex-col md:flex-row gap-2 transition-all focus-within:ring-4 focus-within:ring-red-50">
                <div class="flex-1 relative flex items-center">
                    <div class="absolute left-6 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="infoSearch" placeholder="Cari informasi..." 
                           class="w-full bg-transparent border-0 rounded-2xl py-4 pl-14 pr-6 text-xs md:text-sm font-bold text-gray-900 focus:ring-0 outline-none placeholder:text-gray-300">
                </div>
                <div class="bg-gray-50 px-6 py-4 rounded-[24px] flex items-center gap-3">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Info:</span>
                    <span class="text-sm font-black text-red-700">{{ $informasi->count() }}</span>
                </div>
            </div>
        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="infoGrid">
            @forelse($informasi as $info)
            <div class="info-card bg-white p-8 rounded-[40px] border border-gray-100 hover:border-red-200 hover:shadow-2xl hover:shadow-red-100/20 transition-all duration-500 group flex flex-col justify-between transform hover:-translate-y-2 animasi-kotak delay-100">
                <div>
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-red-700 group-hover:text-white transition-all duration-500">
                            ℹ️
                        </div>
                        <span class="bg-gray-50 text-gray-400 text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest border border-gray-100">Info</span>
                    </div>
                    <h4 class="info-title font-black text-gray-900 text-xl mb-3 uppercase tracking-tight group-hover:text-red-700 transition leading-tight">{{ $info->judul }}</h4>
                    <p class="text-[11px] text-gray-400 mb-10 font-medium leading-relaxed line-clamp-3 group-hover:text-gray-600 transition">{{ $info->deskripsi }}</p>
                </div>
                
                <a href="{{ $info->link }}" target="_blank" class="w-full text-center py-4 rounded-2xl bg-gray-900 text-white font-black text-[10px] uppercase tracking-[0.3em] hover:bg-red-700 transition shadow-xl shadow-gray-200 active:scale-95 flex items-center justify-center gap-2">
                    Buka Informasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
            @empty
            <div class="col-span-3 py-32 text-center opacity-30">
                <p class="text-gray-400 font-black uppercase tracking-widest text-sm">Belum Ada Informasi Yang Ditambahkan</p>
            </div>
            @endforelse
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white py-16 border-t border-gray-50 text-center">
        <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em] mb-3">SIAP Dakwah - Biro Kesekretariatan</p>
        <p class="text-[9px] text-gray-300 font-bold tracking-widest uppercase mb-12">&copy; {{ date('Y') }} Al-Fath Telkom University</p>
    </footer>
</div>

<script>
document.getElementById('infoSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.info-card');
    cards.forEach(card => {
        const title = card.querySelector('.info-title').innerText.toLowerCase();
        card.style.display = title.includes(term) ? 'flex' : 'none';
    });
});
</script>
@endsection
