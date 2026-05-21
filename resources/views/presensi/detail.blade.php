@extends('layout')

@section('content')
<div class="min-h-screen bg-[#f8f5f2] p-8 font-['Inter']">
    {{-- Header Section --}}
    <div class="max-w-7xl mx-auto mb-10">
        {{-- Breadcrumb Row --}}
        <nav class="flex items-center gap-2 mb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-[#7a221f]/60">
            <a href="{{ route('presensi.index') }}" class="hover:text-[#7a221f] transition">
                <span class="hidden md:inline">Dashboard</span>
                <span class="md:hidden">Home</span>
            </a>
            <span>/</span>
            <span class="text-[#7a221f]">
                <span class="hidden md:inline">Manajemen Data</span>
                <span class="md:hidden">Data</span>
            </span>
        </nav>
        
        {{-- Title & Actions Row --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 mb-4">
            <div class="flex items-center gap-4 flex-1">
                <a href="{{ route('presensi.index') }}" class="w-9 h-9 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 transition-all shadow-sm flex items-center justify-center hover:bg-gray-50 active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl md:text-[32px] font-black text-[#1a202c] uppercase tracking-tight leading-none">
                    {{ $sesiInfo->nama_kegiatan ?? 'Daftar Kehadiran' }}
                </h1>
            </div>
            
            <div class="flex flex-wrap items-center justify-start xl:justify-end gap-3 w-full xl:w-auto">
                <form action="{{ route('presensi.detail', $idSesi) }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Cari Nama / NIM..." 
                           class="w-full sm:w-56 pl-10 pr-4 py-2.5 rounded-full border border-gray-200 focus:border-[#7a221f] outline-none text-[11px] font-bold text-gray-600 placeholder-gray-400 shadow-sm bg-white transition-all">
                    <svg class="w-4 h-4 text-[#d97777] absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </form>

                <button type="button" onclick="openCreateModal()" 
                    class="px-5 py-2.5 rounded-full bg-[#50a677] text-white hover:bg-[#438a63] transition-all shadow-md shadow-[#50a677]/20 flex items-center justify-center gap-2 text-[10px] font-bold uppercase tracking-wider active:scale-95 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    <span class="hidden md:inline">Tambah Kader</span>
                    <span class="md:hidden">Tambah</span>
                </button>

                <button type="button" onclick="openDownloadModal()" 
                        class="px-5 py-2.5 rounded-full bg-[#7a221f] text-white hover:bg-[#5a1816] transition-all shadow-md shadow-[#7a221f]/20 flex items-center justify-center gap-2 text-[10px] font-bold uppercase tracking-wider active:scale-95 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span class="hidden md:inline">Cetak PDF</span>
                    <span class="md:hidden">Cetak</span>
                </button>
            </div>
        </div>
        
        {{-- Badges Row --}}
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white px-4 py-1.5 rounded-full shadow-sm border border-gray-200">
                <span class="w-2.5 h-2.5 bg-[#d97777] rounded-full"></span>
                <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">{{ $idSesi }}</p>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-1.5 rounded-full shadow-sm border border-gray-200">
                <svg class="w-3.5 h-3.5 text-[#7a221f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">{{ $sesiInfo->tgl_pelaksanaan ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-[1.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden mb-8 border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px] md:min-w-full">
                    <thead>
                        <tr class="bg-[#7a221f] text-white">
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest border-r border-[#8c2a27]/40 w-1/5">Data Kader</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest border-r border-[#8c2a27]/40 w-1/4">Nama Lengkap</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest border-r border-[#8c2a27]/40">Status</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest border-r border-[#8c2a27]/40">Wajihah</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($daftarHadir as $h)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6 border-r border-gray-100/50 align-top">
                                <p class="text-xs font-black text-[#7a221f] font-mono tracking-widest">{{ $h['nim'] }}</p>
                                <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ $h['email'] }}</p>
                            </td>
                            <td class="px-8 py-6 border-r border-gray-100/50 align-top">
                                <p class="text-sm font-black text-[#2d333b] uppercase tracking-wide group-hover:text-[#7a221f] transition-colors">{{ $h['nama'] }}</p>
                                <div class="mt-2">
                                    <span class="bg-[#f3f4f6] text-[#6b7280] px-2.5 py-1 rounded text-[9px] font-bold uppercase tracking-widest">{{ $h['amanah'] }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 border-r border-gray-100/50 align-top">
                                @php
                                    $statusStyle = [
                                        'Hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'Izin' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'Sakit' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'Alpha' => 'bg-red-50 text-red-600 border-red-100'
                                    ][$h['status']] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-xl text-[9px] font-bold uppercase border shadow-sm {{ $statusStyle }}">
                                    {{ $h['status'] }}
                                </span>
                            </td>
                            <td class="px-8 py-6 border-r border-gray-100/50 align-top">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ $h['wajihah'] }}</span>
                            </td>
                            <td class="px-8 py-6 text-center align-top">
                                <button onclick="openEditModal({{ json_encode($h) }})" 
                                        class="w-10 h-10 mx-auto rounded-full border border-gray-200 bg-white text-gray-400 flex items-center justify-center hover:text-[#7a221f] hover:border-[#7a221f] hover:shadow-sm transition-all group-hover:scale-105 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center opacity-40">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-gray-500 font-bold uppercase tracking-widest text-xs">Belum Ada Data</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($daftarHadir->hasPages())
        <div class="flex justify-center pb-8">
            <div class="bg-white px-4 py-2 rounded-2xl shadow-sm border border-gray-100">
                {{ $daftarHadir->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="editModal" class="fixed inset-0 bg-red-900/40 backdrop-blur-md hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[2rem] p-10 w-full max-w-lg shadow-2xl border border-red-100 transform transition-all overflow-y-auto max-h-[90vh]">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-black text-red-900 uppercase tracking-tight">Koreksi Data</h3>
            <div class="h-1 w-10 bg-red-800 mx-auto mt-2 rounded-full"></div>
        </div>
        
        <form action="{{ route('presensi.update-row') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="row_index" id="modal_row_index">
            <input type="hidden" name="id_sesi" value="{{ $idSesi }}">
            
            <div>
                <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Nama Lengkap</label>
                <input type="text" name="nama" id="modal_nama" required class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-black uppercase text-gray-800">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Amanah</label>
                    <input type="text" name="amanah" id="modal_amanah" placeholder="Staff/BPH" class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-700">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Asal Wajihah</label>
                    <input type="text" name="wajihah" id="modal_wajihah" class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-700">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">NIM Kader</label>
                    <input type="text" name="nim" id="modal_nim" maxlength="12" required class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-mono font-bold text-red-900">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Status Kehadiran</label>
                    <div class="relative">
                        <select name="status" id="modal_status" class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-700 appearance-none">
                            <option value="Hadir">HADIR</option>
                            <option value="Izin">IZIN</option>
                            <option value="Sakit">SAKIT</option>
                            <option value="Alpha">ALPHA</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none mt-1">
                            <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-600 transition">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-red-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-red-200 hover:bg-red-900 transition-all active:scale-95">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DOWNLOAD PDF --}}
<div id="downloadModal" class="fixed inset-0 bg-red-900/40 backdrop-blur-md hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[2rem] p-10 w-full max-w-md shadow-2xl border border-red-100 transform transition-all">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-black text-red-900 uppercase tracking-tight">Cetak Laporan</h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Masukkan Identitas Penanda Tangan</p>
            <div class="h-1 w-10 bg-red-800 mx-auto mt-3 rounded-full"></div>
        </div>
        
        <form action="{{ route('presensi.export-pdf', $idSesi) }}" method="GET" target="_blank" onsubmit="closeDownloadModal()">
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Nama Lengkap Admin</label>
                    <input type="text" name="nama_ttd" required placeholder="Contoh: Ahmad Fathoni" 
                           class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-black uppercase text-gray-800">
                </div>

                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">NIM / NIP</label>
                    <input type="text" name="nim_ttd" required placeholder="Masukkan NIM kamu" 
                           class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-mono font-bold text-red-900">
                </div>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="button" onclick="closeDownloadModal()" class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-600 transition">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-red-800 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-red-200 hover:bg-red-900 transition-all active:scale-95">Download PDF</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TAMBAH PESERTA (CREATE) --}}
<div id="createModal" class="fixed inset-0 bg-red-900/40 backdrop-blur-md hidden items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-[2rem] p-10 w-full max-w-lg shadow-2xl border border-red-100 transform transition-all overflow-y-auto max-h-[90vh]">
        <div class="text-center mb-6">
            <h3 class="text-2xl font-black text-red-900 uppercase tracking-tight">Tambah Peserta</h3>
            <div class="h-1 w-10 bg-red-800 mx-auto mt-2 rounded-full"></div>
        </div>
        
        <form action="{{ route('presensi.store-row') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="id_sesi" value="{{ $idSesi }}">
            
            <div>
                <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="NAMA LENGKAP KADER" class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-black uppercase text-gray-800">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">NIM Kader</label>
                    <input type="text" name="nim" maxlength="12" required placeholder="120XXXXXX" class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-mono font-bold text-red-900">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Status Kehadiran</label>
                    <div class="relative">
                        <select name="status" class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-700 appearance-none">
                            <option value="Hadir">HADIR</option>
                            <option value="Izin">IZIN</option>
                            <option value="Sakit">SAKIT</option>
                            <option value="Alpha">ALPHA</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none mt-1">
                            <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INPUT ASAL WAJIHAH & AMANAH --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Asal Wajihah</label>
                    <div class="relative">
                        <select name="wajihah" required class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-[11px] font-black uppercase text-gray-700 appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Wajihah --</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                            @endforeach
                            <option value="Umum/Non-Wajihah">Umum/Non-Wajihah</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none mt-1">
                            <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-red-800/50 uppercase ml-1 tracking-widest">Amanah</label>
                    <input type="text" name="amanah" required placeholder="STAFF/BPH" class="w-full mt-1.5 p-4 rounded-2xl bg-red-50/30 border border-red-100 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold uppercase text-gray-700">
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeCreateModal()" class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-600 transition">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-200 hover:bg-emerald-700 transition-all active:scale-95">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
<script>
    // --- 1. Fungsi untuk Modal Edit (Tetap Aman) ---
    function openEditModal(data) {
        document.getElementById('modal_row_index').value = data.row_index;
        document.getElementById('modal_nama').value = data.nama;
        document.getElementById('modal_amanah').value = data.amanah;
        document.getElementById('modal_wajihah').value = data.wajihah;
        document.getElementById('modal_nim').value = data.nim;
        document.getElementById('modal_status').value = data.status;

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- 2. Fungsi untuk Modal Download (Tetap Aman) ---
    function openDownloadModal() {
        const modal = document.getElementById('downloadModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDownloadModal() {
        const modal = document.getElementById('downloadModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // --- 3. TAMBAHAN: Fungsi untuk Modal Tambah Kader Baru ---
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        if(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        if(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // --- 4. GABUNGAN LOGIKA KLIK LUAR MODAL (Sudah Update) ---
    window.onclick = function(event) {
        const editModal = document.getElementById('editModal');
        const downloadModal = document.getElementById('downloadModal');
        const createModal = document.getElementById('createModal'); // Tambahkan variabel ini
        
        // Logika: Jika yang diklik adalah background hitamnya, tutup modalnya
        if (event.target == editModal) closeModal();
        if (event.target == downloadModal) closeDownloadModal();
        if (event.target == createModal) closeCreateModal(); // Tambahkan logika ini
    }
</script>
@endsection