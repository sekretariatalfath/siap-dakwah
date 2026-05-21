<div id="lpjModal" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-red-950/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-4xl rounded-[32px] shadow-2xl transform transition-all animate-zoom-in overflow-hidden border border-white/20 my-auto">
        
        {{-- Header Cinematic --}}
        <div class="bg-red-900 px-10 py-8 flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter leading-none">Input Realisasi LPJ</h3>
                <p class="text-[10px] text-red-200 font-bold uppercase tracking-[0.2em] mt-2 opacity-80">Laporan Pertanggungjawaban & Evaluasi Akhir</p>
            </div>
            <button onclick="closeLpjModal()" class="relative z-10 bg-white/10 hover:bg-white/20 p-2 rounded-xl text-white transition-all active:scale-90">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('lpj.store') }}" method="POST" class="p-10 custom-scrollbar max-h-[75vh] overflow-y-auto">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                {{-- KIRI: DATA PROGRAM & NARASI --}}
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Pilih Program Kerja</label>
                        <select name="nama_proker" required class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-red-800 outline-none appearance-none cursor-pointer">
                            <option value="" disabled selected>-- PILIH PROKER --</option>
                            @foreach($proposals as $p)
                                <option value="{{ $p->nama_proker }}">{{ $p->nama_proker }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Realisasi Jumlah Peserta Hadir</label>
                        <input type="number" name="realisasi_peserta" required placeholder="Contoh: 50" class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold focus:ring-2 focus:ring-red-800 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Ketercapaian Tujuan</label>
                        <textarea name="ketercapaian_tujuan" rows="3" required placeholder="Jelaskan secara ringkas bagaimana tujuan program kerja telah tercapai (Misal: Materi tersampaikan dengan baik kepada 50 peserta)." class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold focus:ring-2 focus:ring-red-800 outline-none transition-all"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Realisasi Sasaran Kegiatan</label>
                        <textarea name="realisasi_sasaran" rows="3" required placeholder="Sebutkan siapa saja yang hadir, misal: Pengurus LDF, Mahasiswa FIT angkatan 2023, dsb." class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold focus:ring-2 focus:ring-red-800 outline-none transition-all"></textarea>
                    </div>
                </div>

                {{-- KANAN: ANGGARAN & BERKAS --}}
                <div class="space-y-6">
                    <div class="bg-gray-50 p-6 rounded-[28px] border border-gray-100 space-y-5">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-2">Realisasi Keuangan</p>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500">Anggaran Sponsor (Jika Ada)</label>
                            <input type="number" name="anggaran_sponsor" value="0" class="w-full px-4 py-3 rounded-xl border border-white bg-white text-xs font-black focus:ring-2 focus:ring-red-800 outline-none shadow-sm font-mono text-emerald-600">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500">Total Anggaran Terpakai</label>
                            <input type="number" name="realisasi_anggaran" required placeholder="Masukkan angka tanpa titik, contoh: 500000" class="w-full px-4 py-3 rounded-xl border border-white bg-white text-xs font-black focus:ring-2 focus:ring-red-800 outline-none shadow-sm font-mono text-red-700">
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- LINK EVALUASI --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Tautan Google Docs Evaluasi</label>
                            <input type="url" name="link_evaluasi" required placeholder="Tempelkan Link Google Docs Hasil Evaluasi" class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-[10px] font-bold text-blue-600 focus:ring-2 focus:ring-red-800 outline-none transition-all">
                        </div>
                        {{-- LINK LPJ --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Tautan PDF LPJ (Final)</label>
                            <input type="url" name="link_lpj_pdf" required placeholder="Tempelkan Link Google Drive PDF LPJ" class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-[10px] font-bold text-blue-600 focus:ring-2 focus:ring-red-800 outline-none transition-all">
                        </div>
                        {{-- LINK DOKUMENTASI --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Tautan Folder Dokumentasi</label>
                            <input type="url" name="link_dokumentasi" required placeholder="Tempelkan Link Folder Google Drive Dokumentasi" class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-[10px] font-bold text-blue-600 focus:ring-2 focus:ring-red-800 outline-none transition-all">
                        </div>
                        <div class="flex items-center gap-2 mt-1 ml-1 bg-amber-50 p-2 rounded-lg border border-amber-100">
                            <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            <p class="text-[8px] font-bold text-gray-500 uppercase tracking-tight leading-relaxed">PENTING: Pastikan semua link sudah diset aksesnya ke: <span class="text-amber-700 underline font-black">"Anyone with the link"</span> agar bisa diperiksa.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-8 border-t border-gray-100">
                <button type="submit" onclick="this.disabled=true; this.innerText='SEDANG MENGIRIM...'; this.form.submit();" class="w-full py-5 rounded-[20px] bg-red-900 text-white font-black uppercase tracking-[0.3em] text-xs shadow-2xl shadow-red-900/40 hover:bg-black transition-all active:scale-95 flex justify-center items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    SUBMIT LAPORAN PERTANGGUNGJAWABAN
                </button>
            </div>
        </form>
    </div>
</div>