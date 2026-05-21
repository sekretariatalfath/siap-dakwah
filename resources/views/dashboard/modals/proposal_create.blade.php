<div id="proposalModal" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-red-950/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[32px] shadow-2xl transform transition-all animate-zoom-in overflow-hidden border border-white/20 my-auto">
        
        {{-- Header Cinematic --}}
        <div class="bg-red-900 px-8 py-7 flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <h3 class="text-xl font-black text-white uppercase tracking-tighter leading-none">Pengajuan Proposal</h3>
                <p class="text-[10px] text-red-200 font-bold uppercase tracking-[0.2em] mt-2 opacity-80">LDK Al-Fath Telkom University</p>
            </div>
            <button onclick="closeProposalModal()" class="relative z-10 bg-white/10 hover:bg-white/20 p-2 rounded-xl text-white transition-all active:scale-90">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('proposal.store') }}" method="POST" class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
            @csrf
            
            <div class="space-y-5">
                {{-- KATEGORI --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Kategori Proposal</label>
                    <div class="relative">
                        <select id="kategoriSelect" name="kategori" required onchange="toggleFormLogic()" 
                                class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-red-800 outline-none transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>-- PILIH KATEGORI --</option>
                            <option value="KM">KM (untuk ke DITMAWA)</option>
                            <option value="NON-KM">NON-KM (untuk ke Keuangan PUSAT)</option>
                        </select>
                    </div>
                </div>

                {{-- NAMA PROKER --}}
                <div class="space-y-2">
                    <label id="label_nama" class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Nama Program Kerja</label>
                    <input type="text" name="nama_proker" required placeholder="Contoh: SEMINAR NASIONAL TEKNOLOGI 2026" 
                           class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold focus:ring-2 focus:ring-red-800 outline-none transition-all uppercase placeholder:text-gray-300">
                </div>

                {{-- DESKRIPSI (Dinamis) --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Bentuk Kegiatan</label>
                    <div class="relative">
                        <select name="bentuk_kegiatan" required 
                                class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-red-800 outline-none transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>-- PILIH BENTUK --</option>
                            <option value="KAJIAN">KAJIAN / SYIAR</option>
                            <option value="SEMINAR/WORKSHOP">SEMINAR / WORKSHOP</option>
                            <option value="LOMBA">PERLOMBAAN / COMPETITION</option>
                            <option value="SOSIAL">PENGABDIAN MASYARAKAT</option>
                            <option value="INTERNAL">INTERNAL / UPGRADING</option>
                            <option value="LAINNYA">LAINNYA (LAIN-LAIN)</option>
                        </select>
                    </div>
                </div>

                {{-- TEMPAT --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Tempat Kegiatan</label>
                    <input type="text" name="tempat_kegiatan" required placeholder="Misal: Gedung TULT Lantai 2 / Aula FIT / Online Zoom" 
                           class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-bold focus:ring-2 focus:ring-red-800 outline-none transition-all uppercase">
                </div>

                {{-- ANGGARAN & TARGET --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Pengajuan Anggaran (Rp)</label>
                        <input type="number" name="anggaran" required placeholder="0" 
                               class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-black focus:ring-2 focus:ring-red-800 outline-none transition-all font-mono">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Target Peserta</label>
                        <input type="number" name="target_peserta" required placeholder="0" 
                               class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-xs font-black focus:ring-2 focus:ring-red-800 outline-none transition-all">
                    </div>
                </div>

                {{-- KONTAK PERSON SECTION --}}
                <div class="bg-gray-50 p-6 rounded-[24px] border border-gray-100 space-y-4">
                    <p class="text-[9px] font-black text-gray-700 uppercase tracking-[0.3em] mb-2 text-center">Data Narahubung</p>
                    <input type="text" name="cp_nim_nama" required placeholder="NIM - Nama Lengkap" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-white text-[10px] font-bold focus:ring-2 focus:ring-red-800 outline-none uppercase shadow-sm">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="cp_wa" required placeholder="Nomor WhatsApp" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-white text-[10px] font-bold focus:ring-2 focus:ring-red-800 outline-none shadow-sm">
                        <input type="text" name="cp_line" id="input_line" placeholder="ID LINE" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-white text-[10px] font-bold focus:ring-2 focus:ring-red-800 outline-none shadow-sm">
                    </div>
                    <input type="email" name="cp_email" required placeholder="Alamat Email Aktif" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-white text-[10px] font-bold focus:ring-2 focus:ring-red-800 outline-none shadow-sm">
                </div>

                {{-- LINK PDF --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-gray-700 tracking-widest ml-1">Tautan (Link) PDF Proposal</label>
                    <input type="url" name="link_pdf" required placeholder="Tempelkan Link Google Drive PDF Proposal" 
                           class="w-full px-5 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-[10px] font-bold text-blue-600 focus:ring-2 focus:ring-red-800 outline-none transition-all">
                    <div class="flex items-center gap-2 mt-1 ml-1">
                        <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        <p class="text-[8px] font-bold text-gray-700 uppercase tracking-tight">Pastikan akses link: <span class="text-amber-600 underline">"Siapa saja yang memiliki link"</span></p>
                    </div>
                </div>

                {{-- CHECKBOX --}}
                <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" required class="mt-1 w-4 h-4 rounded border-amber-300 text-red-900 focus:ring-red-800 cursor-pointer">
                        <span class="text-[9px] text-amber-800 font-bold leading-relaxed uppercase">
                            BISMILLAHIRRAHMANIRRAHIM, SAYA MENYATAKAN DATA DI ATAS <span class="underline">BENAR</span> DAN <span class="underline">SESUAI</span> DENGAN ISI DOKUMEN PROPOSAL.
                        </span>
                    </label>
                </div>
            </div>

            <button type="submit" onclick="this.disabled=true; this.innerText='SEDANG MENGIRIM...'; this.form.submit();" class="w-full py-4 rounded-2xl bg-red-900 text-white font-black uppercase tracking-widest text-[10px] shadow-2xl shadow-red-900/40 hover:bg-black transition-all active:scale-95 flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                SUBMIT PROPOSAL SEKARANG
            </button>
        </form>
    </div>
</div>

<script>
function toggleFormLogic() {
    const kategori = document.getElementById('kategoriSelect').value;
    const labelNama = document.getElementById('label_nama');
    const fieldDeskripsi = document.getElementById('field_deskripsi');
    const inputDeskripsi = document.getElementById('input_deskripsi');
    const inputLine = document.getElementById('input_line'); 

    if (kategori === 'NON-KM') {
        labelNama.innerText = "Nama Kegiatan (Sesuai Proposal)";
        fieldDeskripsi.classList.remove('hidden'); // Munculkan deskripsi
        inputDeskripsi.required = true;
        if(inputLine) inputLine.classList.add('hidden'); // Sembunyikan Line ID
    } else {
        labelNama.innerText = "Nama Proker (Sesuai Proposal)";
        fieldDeskripsi.classList.add('hidden'); // Sembunyikan deskripsi
        inputDeskripsi.required = false;
        if(inputLine) inputLine.classList.remove('hidden'); // Munculkan Line ID
    }
}
</script>