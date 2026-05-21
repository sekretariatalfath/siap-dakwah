@extends('layouts.dashboard')

@section('dashboard-content')
<div class="min-h-screen bg-[#f8f5f2] p-6">
    <div class="max-w-3xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 uppercase tracking-tight">Notulensi</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1">Otomatisasi Dokumen Syuro</p>
            </div>

            <div class="flex justify-center md:justify-end">
                
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 {{ $theme['light'] }} border border-red-200 {{ $theme['text'] }} rounded-2xl shadow-sm text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl md:rounded-[2.5rem] shadow-xl shadow-red-900/5 border border-red-50 p-6 md:p-10 animasi-kotak">
            <form action="{{ route('notulensi.generate') }}" method="POST" id="generateForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Judul Syuro / Rapat</label>
                        <input type="text" name="judul_syuro" required placeholder="Misal: Syuro Mingguan" 
                               class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-xs md:text-sm font-black uppercase text-gray-800">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Pimpinan Rapat</label>
                        <input type="text" name="pimpinan_rapat" required placeholder="Nama Lengkap Pimpinan" 
                               class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-xs md:text-sm font-bold text-gray-800 uppercase">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Kategori Syuro</label>
                        <select name="kategori" class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800 uppercase">
                            <option value="Rutin/Koordinasi">Rutin/Koordinasi</option>
                            <option value="Proker KM">Proker KM</option>
                            <option value="Proker Non KM">Proker Non KM</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Waktu Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" value="{{ date('Y-m-d\TH:i') }}" 
                               class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold text-gray-800 uppercase">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-gray-700 uppercase ml-2 tracking-widest">Tempat / Platform</label>
                        <input type="text" name="tempat" required placeholder="Misal: Sekretariat / Zoom" 
                               class="w-full mt-2 p-4 rounded-2xl bg-gray-50 border border-gray-300 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-xs md:text-sm font-bold text-gray-800 uppercase">
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" id="btnSubmit" class="w-full py-5 {{ $theme['bg'] }} text-white rounded-2xl text-xs font-black uppercase tracking-[0.3em] shadow-xl shadow-red-200 {{ $theme['hover'] }} transition-all active:scale-[0.98] flex items-center justify-center gap-3 group">
                        <span id="btnText" class="flex items-center gap-3">
                            <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="hidden md:inline">Generate Google Docs</span>
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

<script>
    const form = document.getElementById('generateForm');
    const btn = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    const loader = document.getElementById('loader');

    form.addEventListener('submit', function() {
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        btnText.classList.add('hidden');
        loader.classList.remove('hidden');
    });
</script>
@endsection