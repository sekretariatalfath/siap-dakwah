@extends('layouts.dashboard')

@section('dashboard-content')


<div class="min-h-screen bg-[#fcfcfc] py-10 px-6 font-inter">
    
    {{-- 1. HEADER AREA --}}
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-6">
        <div>
            <nav class="flex gap-2 text-[10px] font-black uppercase tracking-[0.3em] text-gray-600 mb-3">
                
                <span>/</span>
                <span class="{{ $theme['text'] }}">Proposal Management</span>
            </nav>
            <h1 class="text-2xl md:text-4xl font-black text-gray-900 tracking-tighter uppercase leading-none">
                Arsip <span class="{{ $theme['text'] }}">Proposal</span>
            </h1>
            <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase tracking-widest mt-2">
                KM & NON-KM
            </p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <button onclick="openProposalModal()" class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl shadow-emerald-900/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Ajukan Proposal
            </button>
            
        </div>
    </div>

    {{-- 2. STATS OVERVIEW (Optional but Cinematic) --}}
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-[24px] border border-gray-100 shadow-sm animasi-kotak">
            <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest">Total Pengajuan</p>
            <p class="text-2xl font-black text-gray-900">{{ $dataProposal->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-[24px] border border-gray-100 shadow-sm animasi-kotak delay-100">
            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Terverifikasi</p>
            <p class="text-2xl font-black text-emerald-600">{{ $dataProposal->where('is_checked', true)->count() }}</p>
        </div>
    </div>

    {{-- 3. MAIN TABLE CARD --}}
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-[32px] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden animasi-kotak delay-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px] md:min-w-full">
                    <thead>
                        <tr class="{{ $theme['bg'] }} text-white">
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Status & Waktu</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Detail Program Kerja</th>
                            <th class="hidden md:table-cell px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-center">Penagjuan Anggaran</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($dataProposal as $p)
                        <tr class="hover:bg-gray-50/80 transition-all group">
                            {{-- Kolom Status --}}
                            <td class="px-8 py-7">
                                <p class="text-[10px] font-bold text-gray-600 mb-2">{{ $p->tgl_input }}</p>
                                @if($p->is_checked)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-tighter">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        ✓ Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tighter italic">
                                        Pending
                                    </span>
                                @endif
                                <div class="mt-3 text-[9px] font-black text-gray-300 uppercase tracking-widest">{{ $p->kategori }}</div>
                            </td>

                            {{-- Kolom Program --}}
                            <td class="px-8 py-7">
                                <h4 class="text-sm font-black text-gray-900 uppercase leading-tight group-hover:{{ $theme['text'] }} transition-colors">
                                    {{ $p->nama_proker }}
                                </h4>
                                
                                {{-- TAG BENTUK KEGIATAN --}}
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-[8px] font-black px-2 py-0.5 rounded bg-gray-900 text-white tracking-[0.2em] uppercase">
                                        {{ $p->bentuk_kegiatan }}
                                    </span>
                                    <span class="text-[9px] text-gray-600 font-bold uppercase tracking-tighter italic">
                                        dilaksanakan di {{ $p->tempat }}
                                    </span>
                                </div>

                                <div class="mt-4 flex items-center gap-3">
                                    <span class="text-[9px] font-black text-gray-600 bg-gray-100 px-2 py-1 rounded-lg uppercase">Target: {{ $p->target_peserta }} Mhs</span>
                                    <span class="text-[9px] font-black text-gray-600 bg-gray-100 px-2 py-1 rounded-lg uppercase">{{ $p->pemohon }}</span>
                                </div>
                            </td>

                            {{-- Kolom Anggaran --}}
                            <td class="hidden md:table-cell px-8 py-7 text-center">
                                <div class="inline-block px-4 py-2 rounded-2xl {{ $theme['light'] }} border border-red-100">
                                    <span class="text-sm font-black text-red-900 font-mono italic">
                                        Rp{{ number_format($p->anggaran, 0, ',', '.') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Kolom Aksi --}}
                            <td class="px-8 py-7 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <button onclick="previewProposal({{ json_encode($p) }})" class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Preview Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    <a href="{{ $p->link_pdf }}" target="_blank" class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Buka Link PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>

                                    @if(Auth::user()->role == 'superadmin')
                                    <form action="{{ route('proposal.toggle', $p->proposal_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2.5 rounded-xl transition-all {{ $p->is_checked ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/20' : 'bg-gray-100 text-gray-600 hover:bg-emerald-600 hover:text-white' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('proposal.destroy', $p->proposal_id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus proposal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2.5 {{ $theme['light'] }} text-red-400 rounded-xl hover:{{ $theme['bg'] }} hover:text-white transition-all">
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
                                <div class="flex flex-col items-center justify-center space-y-6">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-red-100 rounded-full blur-2xl opacity-50 animate-pulse"></div>
                                        <div class="relative bg-white p-8 rounded-[40px] border-2 border-gray-50 shadow-xl">
                                            <svg class="w-16 h-16 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Arsip Masih Kosong</h3>
                                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em]">Belum ada pengajuan proposal yang tercatat di sistem.</p>
                                    </div>
                                    <button onclick="openProposalModal()" class="px-6 py-3 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-900 transition-all shadow-lg shadow-gray-900/20 active:scale-95">
                                        Mulai Ajukan Sekarang
                                    </button>
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
@include('dashboard.modals.proposal_create')
@include('dashboard.modals.proposal_preview')

<script>
    function openProposalModal() {
        const modal = document.getElementById('proposalModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeProposalModal() {
        const modal = document.getElementById('proposalModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function previewProposal(data) {
        // Fungsi pembantu agar kodingan rapi
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.innerText = val || '-';
        };

        setVal('pre_id', data.proposal_id);
        setVal('pre_nama', data.nama_proker);
        setVal('pre_bentuk', data.bentuk_kegiatan);
        setVal('pre_deskripsi', data.deskripsi);
        setVal('pre_kategori', data.kategori);
        setVal('pre_tempat', data.tempat);
        setVal('pre_anggaran', 'Rp ' + (parseInt(data.anggaran) || 0).toLocaleString('id-ID'));
        setVal('pre_peserta', (data.target_peserta || 0) + ' Orang');
        setVal('pre_panitia', (data.jumlah_panitia || 0) + ' Orang');

        const linkPdf = document.getElementById('pre_link_pdf');
        if (linkPdf) {
            linkPdf.href = data.link_pdf || '#';
            linkPdf.innerText = "Buka Berkas Proposal ↗";
        }

        setVal('pre_cp_nama', data.cp_nama);
        setVal('pre_cp_wa', data.cp_wa);
        setVal('pre_cp_email', data.cp_email);
        setVal('pre_cp_line', data.cp_line);

        // Tampilkan Modal
        const modal = document.getElementById('previewModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePreviewModal() {
        const modal = document.getElementById('previewModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Overlay click
    window.onclick = function(event) {
        if (event.target.id === 'proposalModal') closeProposalModal();
        if (event.target.id === 'previewModal') closePreviewModal();
    }
</script>

<style>
    .animate-zoom-in { animation: zoomIn 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes zoomIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fee2e2; border-radius: 10px; }
</style>
@endsection