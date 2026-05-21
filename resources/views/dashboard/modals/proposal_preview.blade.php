<div id="previewModal" class="hidden fixed inset-0 z-[70] overflow-y-auto bg-red-950/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[32px] shadow-2xl overflow-hidden border border-white/20 animate-zoom-in my-auto">
        
        {{-- Header Status --}}
        <div class="bg-gray-900 px-8 py-10 text-white relative">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-red-900/20 to-transparent"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div class="space-y-2">
                    <span class="bg-red-800 text-[8px] font-black px-2 py-1 rounded tracking-[0.2em] uppercase inline-block">Proposal Detail</span>
                    <h3 id="pre_nama" class="text-2xl font-black uppercase tracking-tighter leading-tight max-w-[280px]">Nama Program Kerja</h3>
                    <p id="pre_id" class="text-[10px] font-mono text-gray-700 uppercase tracking-widest"></p>
                </div>
                <button onclick="closePreviewModal()" class="text-white/20 hover:text-white transition-all bg-white/5 p-2 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <div class="p-8 space-y-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
            
            {{-- GRID INFO UTAMA --}}
            <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest block mb-1">Kategori Program</label>
                    <p id="pre_kategori" class="text-xs font-black text-gray-800"></p>
                </div>
                
                {{-- INFO BARU: BENTUK KEGIATAN --}}
                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest block mb-1">Bentuk Kegiatan</label>
                    <span id="pre_bentuk" class="inline-block px-2 py-0.5 rounded bg-red-900 text-white text-[9px] font-black tracking-widest uppercase"></span>
                </div>

                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest block mb-1">Anggaran Diajukan</label>
                    <p id="pre_anggaran" class="text-xs font-black text-red-700 font-mono"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest block mb-1">Lokasi / Tempat</label>
                    <p id="pre_tempat" class="text-xs font-black text-gray-800 uppercase"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest block mb-1">Target Peserta</label>
                    <p id="pre_peserta" class="text-xs font-black text-gray-800"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest block mb-1">Jumlah Panitia</label>
                    <p id="pre_panitia" class="text-xs font-black text-gray-800"></p>
                </div>
            </div>

            {{-- SEPARATOR KONTAK --}}
            <div class="space-y-4">
                <p class="text-[9px] font-black text-gray-700 uppercase tracking-[0.3em] text-center">Penanggung Jawab (CP)</p>
                <div class="bg-red-50/30 rounded-3xl p-5 border border-red-50 space-y-3">
                    <div class="flex justify-between items-center border-b border-red-100/50 pb-2">
                        <span class="text-[10px] font-bold text-gray-700 uppercase">Nama / NIM</span>
                        <span id="pre_cp_nama" class="text-[10px] font-black text-gray-800 uppercase"></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-red-100/50 pb-2">
                        <span class="text-[10px] font-bold text-gray-700 uppercase">WhatsApp</span>
                        <span id="pre_cp_wa" class="text-[10px] font-black text-gray-800 font-mono"></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-red-100/50 pb-2">
                        <span class="text-[10px] font-bold text-gray-700 uppercase">E-mail</span>
                        <span id="pre_cp_email" class="text-[10px] font-black text-gray-800"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-700 uppercase">ID Line</span>
                        <span id="pre_cp_line" class="text-[10px] font-black text-gray-800"></span>
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTON --}}
            <a id="pre_link_pdf" href="#" target="_blank" class="block w-full py-4 rounded-2xl bg-emerald-600 text-white text-center font-black uppercase tracking-widest text-[10px] shadow-lg shadow-emerald-900/20 hover:bg-emerald-700 transition-all">
                Buka Berkas Proposal ↗
            </a>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100 flex gap-3">
            <button onclick="closePreviewModal()" class="w-full py-4 rounded-2xl bg-white border border-gray-300 text-gray-500 font-black uppercase tracking-widest text-[10px] hover:bg-gray-100 transition-all active:scale-95 shadow-sm">Tutup Detail</button>
        </div>
    </div>
</div>