@extends('layouts.dashboard')

@section('dashboard-content')


<div class="min-h-screen bg-[#fcfcfc] py-10 px-6 font-inter">
    
    {{-- 1. HEADER AREA --}}
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-6">
        <div>
            <nav class="flex gap-2 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">
                
                <span>/</span>
                <span class="{{ $theme['text'] }} font-black">LPJ Management</span>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase leading-none">
                Arsip <span class="{{ $theme['text'] }}">LPJ</span>
            </h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-2">
                Laporan Pertanggungjawaban & Realisasi Anggaran
            </p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <button onclick="openLpjModal()" class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl shadow-emerald-900/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Input Realisasi LPJ
            </button>
            
        </div>
    </div>

    {{-- 2. MAIN TABLE CARD --}}
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-[32px] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden animasi-kotak">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 text-white">
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Status</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Program Kerja</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-center">Realisasi Anggaran</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($dataLpj as $l)
                        <tr class="hover:bg-gray-50/80 transition-all group">
                            <td class="px-8 py-7">
                                @if($l->is_checked)
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-tighter italic">
                                        ✓ Verified
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tighter italic">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-7">
                                <h4 class="text-sm font-black text-gray-900 uppercase leading-tight group-hover:{{ $theme['text'] }} transition-colors">
                                    {{ $l->nama_proker }}
                                </h4>
                                <p class="text-[9px] text-gray-400 mt-1 font-bold uppercase tracking-tighter">
                                    Diajukan Oleh: {{ $l->pemohon }}
                                </p>
                            </td>
                            <td class="px-8 py-7 text-center">
                                <span class="text-sm font-black text-red-900 font-mono italic">
                                    Rp{{ number_format($l->realisasi_anggaran, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-8 py-7 text-right">
                                <div class="flex justify-end gap-2">
                                    {{-- TOMBOL PREVIEW --}}
                                    <button onclick="previewLpj({{ e(json_encode($l)) }})" class="p-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-900 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    {{-- VERIFIKASI (SUPERADMIN) --}}
                                    @if(Auth::user()->role == 'superadmin' && !$l->is_checked)
                                    <form action="{{ route('lpj.verify', $l->id_lpj) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-2.5 bg-emerald-600 text-white rounded-xl shadow-lg transition-all hover:scale-110">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- DELETE (SUPERADMIN) --}}
                                    @if(Auth::user()->role == 'superadmin')
                                    <form action="{{ route('lpj.destroy', $l->id_lpj) }}" method="POST" onsubmit="return confirm('Hapus data LPJ ini secara permanen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2.5 {{ $theme['light'] }} text-red-500 rounded-xl hover:{{ $theme['bg'] }} hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center">
                                <div class="opacity-20 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm font-black uppercase tracking-[0.3em] text-red-900">Belum Ada Arsip LPJ</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
@include('dashboard.modals.lpj_create')
@include('dashboard.modals.lpj_preview')

{{-- SCRIPTS --}}
<script>
    function openLpjModal() {
        const modal = document.getElementById('lpjModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeLpjModal() {
        const modal = document.getElementById('lpjModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function previewLpj(data) {
        // Mengisi data ke dalam modal preview
        document.getElementById('pre_nama_proker').innerText = data.nama_proker;
        document.getElementById('pre_id_lpj').innerText = data.id_lpj;
        document.getElementById('pre_peserta').innerText = data.realisasi_peserta + ' ORANG';
        document.getElementById('pre_sponsor').innerText = 'Rp ' + parseInt(data.anggaran_sponsor).toLocaleString('id-ID');
        document.getElementById('pre_terpakai').innerText = 'Rp ' + parseInt(data.realisasi_anggaran).toLocaleString('id-ID');
        document.getElementById('pre_tujuan').innerText = data.ketercapaian_tujuan;
        document.getElementById('pre_sasaran').innerText = data.realisasi_sasaran;

        // Set Link Atribut
        document.getElementById('link_pdf').href = data.link_lpj_pdf;
        document.getElementById('link_dok').href = data.link_dokumentasi;
        document.getElementById('link_eva').href = data.link_evaluasi;

        const modal = document.getElementById('previewLpjModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePreviewLpj() {
        const modal = document.getElementById('previewLpjModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    window.onclick = function(event) {
        const lpjModal = document.getElementById('lpjModal');
        const preModal = document.getElementById('previewLpjModal');
        if (event.target == lpjModal) closeLpjModal();
        if (event.target == preModal) closePreviewLpj();
    }
</script>

<style>
    .animate-zoom-in { animation: zoomIn 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes zoomIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fee2e2; border-radius: 10px; }
</style>
@endsection