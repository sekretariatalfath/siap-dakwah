<div id="previewLpjModal" class="hidden fixed inset-0 z-[70] overflow-y-auto bg-gray-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-[32px] shadow-2xl overflow-hidden my-auto animate-zoom-in">
        <div class="bg-gray-900 p-8 text-white flex justify-between items-center">
            <div>
                <h3 id="pre_id_lpj" class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] mb-1">ID LPJ</h3>
                <h2 id="pre_nama_proker" class="text-xl font-black uppercase tracking-tighter">NAMA PROKER</h2>
            </div>
            <button onclick="closePreviewLpj()" class="bg-white/10 hover:bg-white/20 p-2 rounded-xl transition">✕</button>
        </div>

        <div class="p-8 space-y-6">
            {{-- Statistik Realisasi --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    <p class="text-[8px] font-black text-gray-700 uppercase tracking-widest mb-1">Peserta Hadir</p>
                    <p id="pre_peserta" class="text-sm font-black text-gray-900">0</p>
                </div>
                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                    <p class="text-[8px] font-black text-emerald-400 uppercase tracking-widest mb-1">Sponsor</p>
                    <p id="pre_sponsor" class="text-sm font-black text-emerald-700">Rp 0</p>
                </div>
                <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                    <p class="text-[8px] font-black text-red-400 uppercase tracking-widest mb-1">Terpakai</p>
                    <p id="pre_terpakai" class="text-sm font-black text-red-700">Rp 0</p>
                </div>
            </div>

            {{-- Narasi --}}
            <div class="space-y-4">
                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest">Ketercapaian Tujuan</label>
                    <p id="pre_tujuan" class="mt-1 text-xs font-bold text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-2xl">--</p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-700 uppercase tracking-widest">Realisasi Sasaran</label>
                    <p id="pre_sasaran" class="mt-1 text-xs font-bold text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-2xl">--</p>
                </div>
            </div>

            {{-- Links --}}
            <div class="grid grid-cols-1 gap-3 pt-4 border-t border-gray-100">
                <a id="link_pdf" href="#" target="_blank" class="flex items-center justify-between p-4 bg-gray-900 text-white rounded-2xl hover:bg-black transition text-[10px] font-black uppercase tracking-widest">
                    Lihat Berkas PDF LPJ <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <div class="grid grid-cols-2 gap-3">
                    <a id="link_dok" href="#" target="_blank" class="flex items-center justify-center gap-2 p-4 bg-blue-50 text-blue-700 rounded-2xl hover:bg-blue-100 transition text-[9px] font-black uppercase">
                        Dokumentasi
                    </a>
                    <a id="link_eva" href="#" target="_blank" class="flex items-center justify-center gap-2 p-4 bg-purple-50 text-purple-700 rounded-2xl hover:bg-purple-100 transition text-[9px] font-black uppercase">
                        Hasil Evaluasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>