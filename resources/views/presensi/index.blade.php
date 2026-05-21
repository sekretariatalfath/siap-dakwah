@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-7xl mx-auto font-inter">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="border-l-4 border-red-800 pl-4">
            <h1 class="text-xl md:text-3xl font-black text-gray-900 uppercase tracking-tight">
                Presensi
            </h1>
            <p class="text-gray-500 text-[10px] md:text-sm mt-1 uppercase font-bold tracking-widest">
                LDK Al-Fath Telkom University
            </p>
        </div>

        
    </div>

    {{-- ALERT SUKSES --}}
    

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- KOLOM KIRI (2/3): FORMULIR BUAT LINK --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('presensi.generate') }}" method="POST" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 animasi-kotak">
                @csrf
                <div class="{{ $theme['bg'] }} px-6 py-4 flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-white text-base md:text-lg uppercase tracking-tight">Link Presensi</h3>
                        <p class="text-red-100 text-[10px] uppercase font-bold tracking-widest">Otomatisasi Absensi Syuro</p>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    {{-- Ganti bagian input Wajihah Penyelenggara dengan ini --}}
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider">Wajihah Penyelenggara</h4>
                        </div>

                        @if($isKestari)
                            {{-- Jika Superadmin/Kestari: Tampilkan Dropdown --}}
                            <select name="unit_host" class="w-full bg-white border border-gray-200 rounded-lg {{ $theme['ring'] }} focus:{{ $theme['border'] }} py-2 px-3 text-sm transition" required>
                                <option value="" disabled selected>-- Pilih Unit Penyelenggara --</option>
                                @foreach($allUnits as $unit)
                                    <option value="{{ $unit }}">{{ $unit }}</option>
                                @endforeach
                            </select>
                        @else
                            {{-- Jika Unit Biasa: Kunci Input (Readonly) --}}
                            <input type="text" name="unit_host" value="{{ Auth::user()->unit }}" 
                                class="w-full border border-gray-200 rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed py-2 px-3 text-sm font-semibold" 
                                readonly>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kegiatan / Syuro <span class="{{ $theme['text'] }}">*</span></label>
                        <input type="text" name="nama_kegiatan" placeholder="Misal: Syuro Mingguan Kestari" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl {{ $theme['ring'] }} focus:{{ $theme['border'] }} py-2.5 px-4 text-xs md:text-sm transition" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="{{ $theme['text'] }}">*</span></label>
                        <select name="kategori" class="w-full bg-gray-50 border border-gray-200 rounded-lg {{ $theme['ring'] }} focus:{{ $theme['border'] }} py-2.5 px-4 transition" required>
                            <option value="Syuro Rutin/Koordinasi">Syuro Rutin/Koordinasi</option>
                            <option value="Syuro Proker KM">Syuro Proker KM</option>
                            <option value="Kehadiran Event/Acara">Kehadiran Event/Acara</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" onclick="this.disabled=true; this.innerText='GENERATING...'; this.form.submit();" class="w-full {{ $theme['bg'] }} {{ $theme['hover'] }} text-white font-black py-4 rounded-xl shadow-md transition transform active:scale-95 flex justify-center items-center gap-2 uppercase tracking-widest text-xs md:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span class="hidden md:inline">Buat Link Absen</span>
                            <span class="md:hidden">Buat Link</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- KOLOM KANAN (1/3): RIWAYAT LINK --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- SEARCH BAR --}}
            <form action="{{ route('presensi.index') }}" method="GET" class="relative group animasi-kotak delay-100">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Cari acara..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold focus:ring-2 {{ $theme['ring'] }} focus:border-transparent outline-none shadow-sm transition-all">
                <svg class="w-4 h-4 text-gray-400 group-focus-within:{{ $theme['text'] }} absolute left-3 top-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </form>

            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 animasi-kotak delay-200">
                <div class="bg-gray-800 px-5 py-3 flex justify-between items-center text-white">
                    <h3 class="font-bold text-sm uppercase flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Link
                    </h3>
                    <span class="bg-gray-700 text-[10px] px-2 py-0.5 rounded-full border border-gray-600">{{ $semuaSesi->total() }}</span>
                </div>
                
                <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                    @forelse($semuaSesi as $s)
                        <div class="p-4 hover:bg-gray-50 transition group">
                            <div class="mb-2">
                                <p class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:{{ $theme['text'] }} transition">{{ $s->nama_kegiatan }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $s->tgl_pelaksanaan }}</p>
                            </div>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-bold {{ $theme['light'] }} {{ $theme['text'] }} px-2 py-0.5 rounded border border-red-100 uppercase">{{ $s->unit_host }}</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">{{ $s->kategori }}</span>
                                </div>
                                
                                @if($s->is_active == '1')
                                    <span class="flex items-center gap-1 text-[9px] font-bold text-emerald-600">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span> ONLINE
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-[9px] font-bold text-gray-400">OFFLINE</span>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-100">
                                    <input type="text" readonly value="{{ route('presensi.public', $s->id_sesi) }}" 
                                        class="text-[10px] bg-transparent border-none w-full text-gray-500 focus:ring-0 font-mono pl-2" id="link-{{ $s->id_sesi }}">
                                    <button onclick="copyLink('{{ $s->id_sesi }}')" 
                                            class="bg-white border border-gray-200 {{ $theme['text'] }} p-2 rounded-lg hover:{{ $theme['light'] }} transition shadow-sm active:scale-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('presensi.detail', $s->id_sesi) }}" 
                                    class="flex items-center justify-center gap-2 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 text-[9px] font-bold uppercase tracking-wider hover:border-red-200 hover:{{ $theme['text'] }} transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Data Hadir
                                    </a>

                                    <form action="{{ route('presensi.toggle', $s->id_sesi) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-2 rounded-lg text-[9px] font-bold transition-all border shadow-sm {{ $s->is_active == '1' ? 'border-orange-100 bg-orange-50 text-orange-600 hover:bg-orange-100' : 'border-emerald-100 bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">
                                            {{ $s->is_active == '1' ? 'Tutup Akses' : 'Buka Akses' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4 opacity-30">
                                <div class="bg-gray-100 p-6 rounded-[28px] border-2 border-white shadow-inner">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500">Belum ada riwayat link</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- PAGINATION --}}
            @if($semuaSesi->hasPages())
            <div class="mt-4 flex justify-center scale-90">
                <div class="px-3 py-1 bg-white rounded-2xl border border-red-50 shadow-sm">
                    {{ $semuaSesi->links() }}
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
    @keyframes fadeInDown {
        from { opacity: 0; transform: translate3d(0, -20px, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .animate-fade-in-down { animation: fadeInDown 0.5s ease-out; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
    function copyLink(id) {
        var copyText = document.getElementById("link-" + id);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        // Ganti alert bawaan browser dengan gaya yang lebih smooth
        const btn = event.currentTarget;
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        setTimeout(() => { btn.innerHTML = originalIcon; }, 2000);
    }
</script>
@endsection