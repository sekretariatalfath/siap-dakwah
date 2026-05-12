@extends('layouts.dashboard')

@section('dashboard-content')
<div class="min-h-screen bg-[#f8f5f2] p-6">
    {{-- Header --}}
    <div class="max-w-6xl mx-auto mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <nav class="flex mb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-red-900/50">
                
                <span class="mx-2">/</span>
                <span class="{{ $theme['text'] }}">Arsip Notulensi</span>
            </nav>
            <h1 class="text-xl md:text-3xl font-black text-gray-900 uppercase tracking-tight">Riwayat Syuro & Rapat</h1>
            <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase mt-1">Manajemen Dokumen {{ Auth::user()->unit }}</p>
        </div>
        
        <a href="{{ route('notulensi.create') }}" class="px-5 md:px-6 py-2.5 md:py-3 rounded-xl {{ $theme['bg'] }} text-white font-black text-[10px] md:text-xs uppercase tracking-widest {{ $theme['hover'] }} transition shadow-lg shadow-red-900/20 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="hidden md:inline">Buat Notulensi Baru</span>
            <span class="md:hidden">Buat Baru</span>
        </a>
        
    </div>

    

    {{-- Tabel Card --}}
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-3xl shadow-xl shadow-red-900/5 border border-red-50 overflow-hidden animasi-kotak">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[700px] md:min-w-full">
                <thead>
                    <tr class="bg-red-900 text-white">
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest border-r border-red-800/50">Info Rapat</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest border-r border-red-800/50">Kategori</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest border-r border-red-800/50">Akses Dokumen</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-50">
                    @forelse($notulensis as $n)
                    <tr class="hover:{{ $theme['light'] }} transition group">
                        <td class="px-6 py-5 border-r border-red-50/50">
                            <p class="text-sm font-black text-gray-800 uppercase tracking-tight group-hover:{{ $theme['text'] }} transition">{{ $n->judul_syuro }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[9px] text-gray-400 font-bold uppercase">{{ date('d M Y', strtotime($n->waktu_mulai)) }}</span>
                                <span class="text-[9px] {{ $theme['text'] }}/40">•</span>
                                <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $n->tempat }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 border-r border-red-50/50">
                            @php
                                $catColor = [
                                    'Rutin/Koordinasi' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'Proker KM' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Proker Non KM' => 'bg-amber-50 text-amber-700 border-amber-100',
                                ][$n->kategori] ?? 'bg-gray-50 text-gray-600';
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase border {{ $catColor }}">
                                {{ $n->kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-5 border-r border-red-50/50">
                            <div class="flex flex-col gap-2">
                                <a href="{{ $n->link_google_docs }}" target="_blank" class="flex items-center gap-2 text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2zM14 8V3.5L18.5 8H14z"/></svg>
                                    Edit Notulensi
                                </a>
                                @if($n->link_daftar_hadir)
                                <a href="{{ $n->link_daftar_hadir }}" target="_blank" class="flex items-center gap-2 text-[10px] font-black {{ $theme['text'] }} hover:{{ $theme['text'] }} uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Lihat Presensi
                                </a>
                                @else
                                <span class="text-[9px] text-gray-300 italic font-bold uppercase tracking-wider">Presensi belum diupload</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- Tombol Link --}}
                                <button onclick="openLinkModal({{ $n->id }}, '{{ $n->link_daftar_hadir }}')" class="p-2.5 bg-amber-50 text-amber-700 rounded-xl hover:bg-amber-600 hover:text-white transition shadow-sm" title="Update Link Presensi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </button>
                                
                                {{-- Tombol Delete --}}
                                @if(Auth::user()->role == 'superadmin')
                                <form action="{{ route('notulensi.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2.5 {{ $theme['light'] }} {{ $theme['text'] }} rounded-xl hover:{{ $theme['bg'] }} hover:text-white transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center opacity-20">
                            <svg class="w-16 h-16 mx-auto mb-2 {{ $theme['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="font-black uppercase tracking-widest text-xs">Belum ada arsip rapat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

{{-- MODAL UPDATE LINK PRESENSI --}}
<div id="linkModal" class="fixed inset-0 bg-red-900/40 backdrop-blur-md hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[2rem] p-10 w-full max-w-lg shadow-2xl border border-red-100 transform transition-all">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-black text-red-900 uppercase tracking-tight">Update Link Presensi</h3>
            <div class="h-1 w-10 {{ $theme['bg'] }} mx-auto mt-2 rounded-full"></div>
        </div>
        
        <form id="formUpdateLink" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold {{ $theme['text'] }}/50 uppercase ml-1 tracking-widest">Link Google Drive (PDF)</label>
                <input type="url" name="link_daftar_hadir" id="input_link_presensi" required placeholder="https://drive.google.com/..." 
                       class="w-full mt-1.5 p-4 rounded-2xl {{ $theme['light'] }} border border-red-100 focus:bg-white focus:ring-2 {{ $theme['ring'] }} transition-all outline-none text-sm font-bold text-gray-800">
                <p class="text-[9px] text-gray-400 mt-2 px-1 italic">*Upload PDF presensi ke Drive, lalu tempel link-nya di sini agar terarsip otomatis.</p>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeLinkModal()" class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-600 transition">Batal</button>
                <button type="submit" class="flex-1 py-4 {{ $theme['bg'] }} text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-red-200 {{ $theme['hover'] }} transition-all active:scale-95">Simpan Link</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLinkModal(id, currentLink) {
        const modal = document.getElementById('linkModal');
        const form = document.getElementById('formUpdateLink');
        const input = document.getElementById('input_link_presensi');
        
        // Menggunakan id untuk menentukan route POST ke updateLink
        form.action = `/notulensi/update-link/${id}`;
        
        // Handle currentLink jika kosong
        input.value = (currentLink && currentLink !== 'null') ? currentLink : '';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLinkModal() {
        const modal = document.getElementById('linkModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection