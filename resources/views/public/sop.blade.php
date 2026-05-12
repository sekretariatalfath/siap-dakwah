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
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:inline">Knowledge Base Al-Fath</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest md:hidden">SOP & Panduan</span>
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
    <header class="bg-gradient-to-br from-red-900 to-red-700 text-white py-20 px-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="container mx-auto relative z-10">
            <h1 class="text-2xl md:text-5xl font-black mb-4 uppercase tracking-tighter leading-none animate-in fade-in slide-in-from-bottom-4 duration-700">SOP & Panduan Administrasi</h1>
            <p class="text-red-100 max-w-2xl mx-auto text-[10px] md:text-sm font-medium uppercase tracking-widest leading-relaxed opacity-80 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-100">
                Pedoman tata kelola organisasi untuk mewujudkan administrasi yang tertib, akuntabel, dan profesional.
            </p>
        </div>
    </header>

    <main class="container mx-auto px-6 py-12 -mt-16 relative z-20">
        {{-- SEARCH BAR --}}
        <div class="max-w-4xl mx-auto mb-10 animasi-kotak">
            <div class="bg-white p-2 rounded-[32px] shadow-2xl shadow-gray-200/40 border border-gray-100 flex items-center transition-all focus-within:ring-4 focus-within:ring-red-50">
                <div class="pl-6 text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="sopSearch" placeholder="Cari panduan..." 
                       class="w-full bg-transparent border-0 rounded-2xl py-4 px-4 text-xs md:text-sm font-bold text-gray-900 focus:ring-0 outline-none placeholder:text-gray-300">
            </div>
        </div>

        {{-- TIPS SECTION --}}
        <div class="max-w-4xl mx-auto mb-16 animasi-kotak delay-100">
            <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm flex flex-col md:flex-row items-center gap-8 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition duration-700"></div>
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl flex-shrink-0 flex items-center justify-center text-3xl shadow-inner relative z-10">📜</div>
                <div class="relative z-10 text-center md:text-left">
                    <h4 class="font-black text-gray-900 text-xs uppercase tracking-widest mb-2">Penting Diketahui</h4>
                    <p class="text-[11px] text-gray-500 font-medium leading-relaxed max-w-xl">
                        Seluruh alur dalam SOP ini bersifat mengikat bagi seluruh fungsionaris LDK Al-Fath. Jika ada kendala dalam implementasi, silakan hubungi Biro Kesekretariatan.
                    </p>
                </div>
            </div>
        </div>

        {{-- DYNAMIC DOCS --}}
        <h3 class="text-base md:text-lg font-black text-gray-800 mb-8 flex items-center gap-3 uppercase tracking-tight">
            <span class="w-1.5 h-6 bg-gray-900 rounded-full"></span>
            Kumpulan Pedoman & SOP
        </h3>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="sopGrid">
            @forelse($sop as $item)
            <div class="sop-card bg-white p-8 rounded-[40px] border border-gray-100 hover:border-red-100 hover:shadow-2xl hover:shadow-red-100/30 transition-all duration-500 group flex flex-col justify-between transform hover:-translate-y-2 animasi-kotak delay-200">
                <div>
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-14 h-14 bg-gray-50 text-gray-900 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-red-700 group-hover:text-white transition-all duration-500 shadow-sm">
                            📖
                        </div>
                        <span class="bg-gray-50 text-gray-400 text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest border border-gray-100 group-hover:bg-red-50 group-hover:text-red-400 transition">Panduan</span>
                    </div>
                    <h4 class="sop-title font-black text-gray-900 text-xl mb-3 uppercase tracking-tight leading-tight group-hover:text-red-700 transition">{{ $item->judul }}</h4>
                    <p class="text-[11px] text-gray-400 mb-10 font-medium leading-relaxed line-clamp-3 group-hover:text-gray-500 transition">{{ $item->deskripsi }}</p>
                </div>
                <a href="{{ $item->link }}" target="_blank" class="w-full text-center py-4 rounded-2xl bg-gray-900 text-white font-black text-[10px] uppercase tracking-[0.3em] hover:bg-red-700 transition shadow-xl shadow-gray-200 group-hover:shadow-red-200 active:scale-95">
                    Buka Panduan
                </a>
            </div>
            @empty
            <div class="col-span-3 py-24 text-center opacity-30 italic text-gray-400 font-black text-xs uppercase tracking-widest">
                Belum ada panduan tersedia.
            </div>
            @endforelse
        </div>
    </main>

    <footer class="bg-white py-16 border-t border-gray-50 text-center">
        <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em] mb-3">SIAP Dakwah - Biro Kesekretariatan</p>
        <p class="text-[9px] text-gray-300 font-bold tracking-widest uppercase mb-12">&copy; {{ date('Y') }} Al-Fath Telkom University</p>
    </footer>
</div>

<script>
document.getElementById('sopSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.sop-card');
    cards.forEach(card => {
        const title = card.querySelector('.sop-title').innerText.toLowerCase();
        card.style.display = title.includes(term) ? 'flex' : 'none';
    });
});
</script>
@endsection