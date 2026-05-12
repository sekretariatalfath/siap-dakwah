@extends('layouts.dashboard')

@section('dashboard-content')


<div class="max-w-6xl mx-auto py-8 px-4">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Arsip <span class="{{ $theme['text'] }}">Surat Keluar</span></h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan update link Google Drive untuk dokumen yang telah digenerate.</p>
        </div>
        
    </div>

    

    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden animasi-kotak">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px] md:min-w-full">
            <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase tracking-widest font-black">
                <tr>
                    <th class="px-6 py-4">Rentang Nomor</th>
                    <th class="px-6 py-4">Detail Perihal</th>
                    <th class="px-6 py-4">Link Drive (Folder/File)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($groups as $batchId => $perihalGroups)
                    @foreach($perihalGroups as $perihal => $items)
                        @php 
                            $first = $items->first(); 
                            $last = $items->last();
                            $currentLink = ($first->link_drive && $first->link_drive != '-') ? $first->link_drive : '';
                        @endphp
                        
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    {{-- PERBAIKAN: Pakai no_surat_full --}}
                                    <span class="text-[10px] font-mono font-bold {{ $theme['text'] }} {{ $theme['light'] }} px-2 py-0.5 rounded w-fit border border {{ $theme['border'] }} opacity-50">
                                        {{ $first->no_surat_full }}
                                    </span>
                                    <span class="text-[8px] text-gray-400 ml-4 font-bold uppercase tracking-tighter">s/d</span>
                                    <span class="text-[10px] font-mono font-bold {{ $theme['text'] }} {{ $theme['light'] }} px-2 py-0.5 rounded w-fit border border {{ $theme['border'] }} opacity-50">
                                        {{ $last->no_surat_full }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800 leading-snug">{{ $perihal }}</p>
                                
                                <div class="flex flex-wrap gap-2 mt-2 items-center">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full {{ $theme['bg'] }} text-white font-bold">
                                        {{ $items->count() }} Surat
                                    </span>

                                    @if(Auth::user()->role == 'superadmin')
                                    <div class="flex items-center gap-1 text-[9px] text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                                        <span class="font-bold uppercase">{{ $first->asal_pengisi }}</span>
                                    </div>
                                    @endif

                                    <span class="text-[8px] text-gray-300 font-medium tracking-widest">BATCH: {{ $batchId }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('surat.arsip.update') }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="batch_id" value="{{ trim($batchId) }}">
                                    <input type="hidden" name="perihal" value="{{ trim($perihal) }}">
                                    
                                    <div class="relative flex-1">
                                        <input type="url" name="link_drive" value="{{ $currentLink }}" 
                                            placeholder="https://drive.google.com/..." 
                                            class="w-full text-[10px] border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 pr-8 py-2">
                                        
                                        @if($currentLink)
                                        <a href="{{ $currentLink }}" target="_blank" class="absolute right-2 top-2 text-blue-500 hover:text-blue-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                        @endif
                                    </div>
                                    
                                    <button type="submit" class="{{ $theme['bg'] }} {{ $theme['hover'] }} text-white text-[9px] px-3 py-2 rounded-lg font-black uppercase transition shadow-sm">
                                        {{ $currentLink ? 'Update' : 'Simpan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-20 text-center text-gray-400 italic">
                            <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            Belum ada arsip surat keluar yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection