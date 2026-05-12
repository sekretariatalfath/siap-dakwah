@extends('layouts.dashboard')

@section('dashboard-content')


<div class="min-h-screen bg-[#fcfcfc] py-10 px-6 font-inter">
    
    {{-- 1. HEADER AREA --}}
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-6">
        <div>
            <nav class="flex gap-2 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-3">
                
                <span>/</span>
                <span class="{{ $theme['text'] }} font-black">Arsip Evaluasi</span>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase leading-none">
                Riwayat <span class="{{ $theme['text'] }}">Evaluasi</span>
            </h1>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-2">
                Manajemen Dokumen {{ Auth::user()->role == 'superadmin' ? 'Pusat' : Auth::user()->unit }}
            </p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <a href="{{ route('evaluasi.generate.form') }}" class="flex-1 md:flex-none {{ $theme['bg'] }} {{ $theme['hover'] }} text-white px-8 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Generate Baru
            </a>
            
        </div>
    </div>

    {{-- 2. ALERT SECTION --}}
    

    {{-- 3. MAIN TABLE CARD --}}
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-[32px] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden animasi-kotak">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 text-white">
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Info Proker & Unit</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Tempat Evaluasi</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Kategori</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em]">Akses G-Docs</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($dataEvaluasi as $e)
                        <tr class="hover:bg-gray-50/80 transition-all group">
                            <td class="px-8 py-7">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <h4 class="text-sm font-black text-gray-800 uppercase leading-tight group-hover:{{ $theme['text'] }} transition-colors">
                                            {{ $e->nama_proker }}
                                        </h4>
                                        {{-- Badge Kode Unit --}}
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[8px] font-black rounded-md border border-gray-200 uppercase">
                                            {{ $e->pemohon }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Rapat: {{ $e->tgl_rapat }}</span>
                                        <span class="text-[9px] text-gray-200">•</span>
                                        <span class="text-[9px] font-mono text-gray-300 uppercase tracking-widest">ID: {{ $e->id_eval }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-7">
                                <div class="flex items-center gap-1.5 text-gray-500">
                                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-tight">{{ $e->tempat ?? 'Belum Terdata' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-7">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black border {{ $e->kategori == 'KM' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} uppercase tracking-tighter">
                                    {{ $e->kategori }}
                                </span>
                            </td>
                            <td class="px-8 py-7">
                                <a href="{{ $e->link_doc }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2zM14 8V3.5L18.5 8H14z"/></svg>
                                    Edit Dokumen
                                </a>
                            </td>
                            <td class="px-8 py-7 text-right">
                                @if(Auth::user()->role == 'superadmin')
                                <form action="{{ route('evaluasi.destroy', $e->id) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 {{ $theme['light'] }} text-red-400 rounded-xl hover:{{ $theme['bg'] }} hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @else
                                <div class="flex items-center justify-end gap-1 opacity-20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span class="text-[8px] font-black uppercase italic">Read Only</span>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center">
                                <div class="opacity-20 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm font-black uppercase tracking-[0.3em] text-red-900">Belum Ada Arsip Evaluasi</p>
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
@endsection