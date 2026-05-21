@extends('layouts.dashboard')

@section('dashboard-content')
<div class="min-h-screen bg-[#f8f5f2] p-6 font-inter">
    <div class="max-w-5xl mx-auto">
        
        {{-- HEADER AREA --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 uppercase tracking-tight">Evaluasi</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1">Otomatisasi Berkas Proker</p>
            </div>

            <div class="flex justify-center md:justify-end">
                
            </div>
        </div>

        <div class="bg-white rounded-3xl md:rounded-[2.5rem] shadow-xl shadow-red-900/5 border border-red-50 p-6 md:p-10 animasi-kotak">
            @if(session('error'))
                <div class="mb-6 {{ $theme['light'] }} border border-red-200 {{ $theme['text'] }} px-6 py-4 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="bg-red-100 p-2 rounded-full">
                        <svg class="w-4 h-4 {{ $theme['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <p class="text-xs font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('evaluasi.generate.submit') }}" method="POST" id="generateForm" class="space-y-8">
                @csrf
                
                {{-- SECTION 1: IDENTITAS PROKER --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Dept / Fakultas / Unit</label>
                        {{-- LOGIKA LOCK UNIT --}}
                        @if(Auth::user()->role == 'superadmin')
                            <select name="dept_fakultas" required class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800 uppercase">
                                @foreach($units as $u)
                                    <option value="{{ $u }}" {{ Auth::user()->unit == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="relative">
                                <input type="text" name="dept_fakultas" value="{{ Auth::user()->unit }}" readonly 
                                    class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-red-100 outline-none text-sm font-bold text-gray-400 uppercase cursor-not-allowed">
                                <svg class="w-4 h-4 absolute right-4 top-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Kategori Proker</label>
                        <select name="kategori" class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800">
                            <option value="KM">KM (DITMAWA)</option>
                            <option value="NON-KM">NON-KM (LD PUSAT)</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Nama Program Kerja</label>
                        <input type="text" name="nama_proker" required placeholder="Misal: TRAINING 2026" 
                               class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-xs md:text-sm font-black uppercase text-gray-800">
                    </div>
                </div>

                <div class="h-px {{ $theme['light'] }}"></div>

                {{-- SECTION 2: WAKTU & TEMPAT PELAKSANAAN --}}
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6"> 
                    
                    {{-- Mulai & Selesai Acara --}}
                    <div class="md:col-span-2 grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Mulai Acara</label>
                            <input type="date" name="tgl_mulai" required 
                                class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Selesai Acara</label>
                            <input type="date" name="tgl_selesai" required 
                                class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800">
                        </div>
                    </div>

                    {{-- Tempat Kegiatan --}}
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Tempat Kegiatan</label>
                        <input type="text" name="tempat_kegiatan" required placeholder="Misal: Aula / Zoom" 
                            class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-xs md:text-sm font-bold text-gray-800 uppercase">
                    </div>

                    {{-- Tempat Evaluasi --}}
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Tempat Evaluasi</label>
                        <input type="text" name="tempat_evaluasi" required placeholder="Misal: Sekre Al-Fath" 
                            class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-xs md:text-sm font-bold text-gray-800 uppercase">
                    </div>

                    {{-- Tgl Evaluasi --}}
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Tgl Evaluasi</label>
                        <input type="date" name="tgl_evaluasi" required 
                            class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800">
                    </div>

                    {{-- Pimpinan Evaluasi --}}
                    <div class="md:col-span-1">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Pimpinan Evaluasi</label>
                        <input type="text" name="pimpinan_evaluasi" required placeholder="Nama Lengkap Pimpinan" 
                            class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-xs md:text-sm font-bold text-gray-800 uppercase">
                    </div>
                    
                </div>

                <div class="h-px {{ $theme['light'] }}"></div>

                {{-- SECTION 3: PENANGGUNG JAWAB --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Ketua Pelaksana</label>
                        <input type="text" name="ketuplak" required placeholder="Nama Lengkap Ketua" 
                               class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800 uppercase">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Sekretaris Proker</label>
                        <input type="text" name="sekre_proker" required placeholder="Nama Lengkap Sekretaris" 
                               class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800 uppercase">
                    </div>
                </div>

                {{-- BUTTON GENERATE --}}
                <div class="pt-4">
                    <button type="submit" id="btnSubmit" class="w-full py-5 {{ $theme['bg'] }} text-white rounded-2xl text-xs font-black uppercase tracking-[0.3em] shadow-xl shadow-red-200 {{ $theme['hover'] }} transition-all active:scale-[0.98] flex items-center justify-center gap-3 group">
                        <span id="btnText" class="flex items-center gap-3">
                            <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="hidden md:inline">Generate Dokumen Evaluasi</span>
                            <span class="md:hidden">GENERATE</span>
                        </span>
                        <svg id="loader" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT LOADING STATE --}}
<script>
    document.getElementById('generateForm').onsubmit = function() {
        document.getElementById('btnText').classList.add('hidden');
        document.getElementById('loader').classList.remove('hidden');
        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('btnSubmit').classList.add('opacity-75', 'cursor-not-allowed');
    };
</script>
@endsection