@extends('layouts.dashboard')

@section('dashboard-content')

{{-- Header Halaman --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="p-3 bg-white rounded-2xl shadow-sm border border-gray-100 text-gray-600 hover:text-gray-900 transition transform hover:-translate-x-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Daftar {{ $category }}</h1>
            <p class="text-xs text-gray-500">Database administrasi tersinkronisasi Spreadsheet.</p>
        </div>
    </div>

    @if(Auth::user()->role == 'superadmin')
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-gray-900 text-white px-6 py-3 rounded-2xl text-xs font-bold uppercase shadow-lg hover:bg-black transition w-full md:w-auto transform hover:-translate-y-1">
        + Tambah Data Baru
    </button>
    @endif
</div>

{{-- Tabel Sumber Daya --}}
<div class="bg-white shadow-sm rounded-[32px] overflow-hidden border border-gray-100 animasi-kotak">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px] md:min-w-full">
            <thead class="bg-gray-50 text-gray-600 text-[10px] uppercase font-bold tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5">Nama Berkas</th>
                    <th class="px-8 py-5">Deskripsi</th>
                    <th class="px-8 py-5">Akses</th>
                    <th class="px-8 py-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($resources as $item)
                    @if(isset($item->judul) && trim($item->judul) != '')
                    <tr class="hover:bg-gray-50/50 transition group">
                        <td class="px-8 py-6">
                            <span class="font-bold text-gray-800 block text-base">{{ $item->judul }}</span>
                        </td>
                        <td class="px-8 py-6 text-gray-500 leading-relaxed max-w-md text-xs font-medium">
                            {{ $item->deskripsi ?? 'Tidak ada keterangan.' }}
                        </td>
                        <td class="px-8 py-6">
                            @if(($item->akses ?? 'Internal') == 'Publik')
                                <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border border-emerald-100">Publik</span>
                            @else
                                <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border border-gray-100">Internal</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 flex justify-end gap-3 items-center">
                            <a href="{{ $item->link }}" target="_blank" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-[10px] font-bold uppercase hover:bg-blue-600 hover:text-white transition shadow-sm">
                                Lihat File
                            </a>
                            
                            @if(Auth::user()->role == 'superadmin')
                            <form action="{{ route('resource.delete', $item->row_index) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf 
                                @method('DELETE')
                                <input type="hidden" name="category" value="{{ $category }}">
                                
                                <button type="submit" class="p-2 text-gray-600 hover:{{ $theme['text'] }} transition hover:{{ $theme['light'] }} rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-32">
                            <div class="flex flex-col items-center justify-center w-full">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-200 mb-4 border border-gray-100 shadow-inner">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-gray-600 italic text-sm font-bold uppercase tracking-widest">Belum ada data tersedia di kategori ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH DATA --}}
<div id="modalAdd" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center p-4 z-50 backdrop-blur-sm">
    <div class="bg-white rounded-[40px] p-8 md:p-10 w-full max-w-lg shadow-2xl relative animate-in fade-in zoom-in duration-300">
        <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="absolute top-6 right-6 text-gray-600 hover:text-gray-900 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-xl font-bold text-gray-900 mb-1 uppercase tracking-tight">Input {{ $category }} Baru</h3>
        <p class="text-xs text-gray-600 mb-8 font-medium italic">Data otomatis masuk ke tab Spreadsheet terkait.</p>

        <form action="{{ route('resource.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="category" value="{{ $category }}">
            
            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase ml-2 tracking-widest">Judul Berkas</label>
                <input type="text" name="judul" required placeholder="Misal: Format Proposal Kegiatan" class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-gray-900 outline-none shadow-inner transition">
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase ml-2 tracking-widest">Keterangan Singkat</label>
                <textarea name="deskripsi" rows="3" required placeholder="Tuliskan detail singkat mengenai berkas ini agar mudah dipahami pengurus lain." class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-gray-900 outline-none shadow-inner transition"></textarea>
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase ml-2 tracking-widest">Tautan (Link) Akses Google Drive</label>
                <input type="url" name="link" required placeholder="Tempelkan Link Google Drive" class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-gray-900 outline-none shadow-inner transition">
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase ml-2 tracking-widest">Status Akses Berkas</label>
                <select name="akses" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-gray-900 outline-none shadow-inner transition appearance-none">
                    <option value="Internal">Hanya Pengurus (Wajib Login)</option>
                    <option value="Publik">Publik (Bisa diakses tanpa login)</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-4 {{ $theme['bg'] }} text-white text-xs font-bold uppercase rounded-2xl shadow-xl hover:brightness-110 transition-all transform hover:-translate-y-1">
                    Simpan ke Spreadsheet
                </button>
            </div>
        </form>
    </div>
</div>

@endsection