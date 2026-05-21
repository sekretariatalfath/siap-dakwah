@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-7xl mx-auto font-inter">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="border-l-4 {{ $theme['border'] }} pl-4">
            <h1 class="text-xl md:text-3xl font-black text-gray-900 tracking-tight uppercase">
                Nomor Surat
            </h1>
            <p class="text-gray-500 text-[10px] md:text-sm mt-1 uppercase font-bold tracking-widest">
                LDK Al-Fath Telkom University
            </p>
        </div>

        
    </div>

    {{-- ALERT SUKSES --}}
    

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- KOLOM KIRI: FORMULIR INPUT --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('surat.store') }}" method="POST" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 animasi-kotak">
                @csrf
                
                <div class="{{ $theme['bg'] }} px-6 py-4 flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-white text-base md:text-lg uppercase tracking-tight">Input Data Surat</h3>
                        <p class="text-white/80 text-[10px] uppercase font-bold tracking-widest">Sistem Al-Fath Gen 13</p>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    {{-- 0. IDENTITAS PENGISI --}}
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <h4 class="font-bold text-gray-700 text-xs uppercase mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Identitas Pengurus
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Pengisi <span class="{{ $theme['text'] }}">*</span></label>
                                <input type="text" name="nama_pengisi" value="{{ Auth::user()->name }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg {{ $theme['ring'] }} py-2 px-3 text-sm transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Unit / LDF (Terkunci)</label>
                                <input type="text" value="{{ Auth::user()->unit }}" class="w-full border border-gray-200 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed py-2 px-3 text-sm" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- 1. JENIS SURAT --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Jenis Surat <span class="{{ $theme['text'] }}">*</span></label>
                        <select name="jenis" class="w-full border border-gray-200 rounded-lg {{ $theme['ring'] }} py-2.5 px-4 bg-gray-50 transition" required>
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="SK">SK - Surat Keputusan</option>
                            <option value="UND">UND - Undangan</option>
                            <option value="LOG">LOG - Peminjaman Logistik</option>
                            <option value="KET">KET - Keterangan</option>
                            <option value="SI">SI - Izin</option>
                            <option value="ST">ST - Surat Tugas</option>
                            <option value="SRT">SRT - Sertifikat</option>
                            <option value="SPB">SPB - Pemberitahuan</option>
                            <option value="MOU">MOU - Kerjasama (MOU)</option>
                            <option value="SPD">SPD - Permohonan Dana</option>
                        </select>
                    </div>

                    {{-- 2. LINGKUP & FAKULTAS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lingkup Surat <span class="{{ $theme['text'] }}">*</span></label>
                            <select name="lingkup" id="lingkup" onchange="updateForm()" class="w-full border border-gray-200 rounded-lg {{ $theme['ring'] }} py-2.5 px-4 bg-gray-50 transition" required>
                                <option value="" disabled selected>-- Pilih Lingkup --</option>
                                @if(Auth::user()->role == 'superadmin' || Auth::user()->unit == 'Biro Kesekretariatan')
                                    <option value="PUSAT">PUSAT (Biro/Departemen)</option>
                                    <option value="FAKULTAS">FAKULTAS (LDF)</option>
                                @elseif(str_contains(Auth::user()->unit, 'Fakultas') || str_contains(Auth::user()->unit, 'LDF'))
                                    <option value="FAKULTAS">FAKULTAS (LDF)</option>
                                @else
                                    <option value="PUSAT">PUSAT (Biro/Departemen)</option>
                                @endif
                            </select>
                        </div>

                        <div id="wrapper-fakultas" class="hidden animate-fade-in">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Asal Fakultas <span class="{{ $theme['text'] }}">*</span></label>
                            <select name="kode_fakultas" id="kode_fakultas" class="w-full border border-gray-200 rounded-lg {{ $theme['ring'] }} py-2.5 px-4 bg-gray-50 shadow-sm">
                                <option value="" disabled selected>-- Pilih Fakultas --</option>
                                @foreach($ldfUnits as $unit)
                                    @php
                                        $u = strtoupper($unit);
                                        $shortCode = "UNIT";
                                        if(str_contains($u, 'INFORMATIKA')) $shortCode = "FIF";
                                        elseif(str_contains($u, 'ELEKTRO')) $shortCode = "FTE";
                                        elseif(str_contains($u, 'INDUSTRI')) $shortCode = "FRI";
                                        elseif(str_contains($u, 'EKONOMI')) $shortCode = "FEB";
                                        elseif(str_contains($u, 'KOMUNIKASI') || str_contains($u, 'SOSIAL')) $shortCode = "FKS";
                                        elseif(str_contains($u, 'KREATIF')) $shortCode = "FIK";
                                        elseif(str_contains($u, 'TERAPAN')) $shortCode = "FIT";
                                        elseif(str_contains($u, 'KEDOKTERAN')) $shortCode = "FKD";
                                        else {
                                            $words = explode(' ', $unit);
                                            $shortCode = "";
                                            foreach($words as $w) { if(!empty($w)) $shortCode .= strtoupper($w[0]); }
                                            $shortCode = substr($shortCode, -3);
                                        }
                                    @endphp
                                    <option value="{{ $shortCode }}">{{ $shortCode }} - {{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 3. KATEGORI --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Format <span class="{{ $theme['text'] }}">*</span></label>
                        <select name="kategori_pusat" id="kategori_pusat" onchange="toggleDetails('PUSAT')" class="hidden w-full border border-gray-200 rounded-lg {{ $theme['ring'] }} py-2.5 px-4 bg-gray-50">
                            <option value="" disabled selected>-- Kategori Pusat --</option>
                            <option value="MSP">MSP - Majelis Musyawarah Pusat</option>
                            <option value="UMUM">UMUM - Administrasi Umum</option>
                            <option value="PROKER_NON_KM">PROKER NON KM (Departemen)</option>
                            <option value="PROKER_KM">PROKER KM (Acara Besar)</option>
                        </select>

                        <select name="kategori_fakultas" id="kategori_fakultas" onchange="toggleDetails('FAKULTAS')" class="hidden w-full border border-gray-200 rounded-lg {{ $theme['ring'] }} py-2.5 px-4 bg-gray-50">
                            <option value="" disabled selected>-- Kategori Fakultas --</option>
                            <option value="UMUM">UMUM - Administrasi Umum LDF</option>
                            <option value="PROKER_NON_KM">PROKER NON KM (Bidang)</option>
                            <option value="PROKER_KM">PROKER KM (Acara Besar)</option>
                        </select>
                    </div>

                    {{-- 4. DETAIL DINAMIS (ACARA/DEPT/BIDANG) --}}
                    <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-200 hidden animate-fade-in" id="wrapper-details">
                        <div id="input-acara" class="hidden">
                            <label class="block text-xs font-bold text-yellow-800 uppercase mb-2">Nama Acara (Otomatis Kapital)</label>
                            <input type="text" name="nama_acara" placeholder="Contoh: ECAFEST" class="w-full bg-gray-50 border border-yellow-300 rounded-lg py-2 px-3 text-sm uppercase focus:ring-yellow-500">
                        </div>

                        <div id="input-dept" class="hidden">
                            <label class="block text-xs font-bold text-blue-800 uppercase mb-2">Pilih Departemen</label>
                            <select name="kode_departemen" class="w-full bg-gray-50 border border-blue-300 rounded-lg py-2 px-3 text-sm">
                                @foreach($pusatUnits as $unit)
                                    @php
                                        $u = strtoupper($unit);
                                        $shortCode = "DEPT";
                                        if(str_contains($u, 'KESEKRETARIATAN')) $shortCode = "KES";
                                        elseif(str_contains($u, 'KEUANGAN')) $shortCode = "KEU";
                                        elseif(str_contains($u, 'KADERISASI')) $shortCode = "KDR";
                                        elseif(str_contains($u, 'SYIAR')) $shortCode = "SYR";
                                        elseif(str_contains($u, 'MEDIA') || str_contains($u, 'MEDKOMINFO')) $shortCode = "MDK";
                                        else {
                                            $words = explode(' ', $unit);
                                            $shortCode = "";
                                            foreach($words as $w) { if(!empty($w)) $shortCode .= strtoupper($w[0]); }
                                            $shortCode = substr($shortCode, 0, 3);
                                        }
                                    @endphp
                                    <option value="{{ $shortCode }}">{{ $shortCode }} - {{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="input-bidang" class="hidden">
                            <label class="block text-xs font-bold text-purple-800 uppercase mb-2">Pilih Bidang LDF</label>
                            <select name="kode_bidang" class="w-full bg-gray-50 border border-purple-300 rounded-lg py-2 px-3 text-sm">
                                <option value="01">01 - Kaderisasi</option>
                                <option value="02">02 - Syiar</option>
                                <option value="03">03 - Media</option>
                                <option value="04">04 - Akademik/Lainnya</option>
                            </select>
                        </div>
                    </div>

                    {{-- 5. INFO TAMBAHAN & JUMLAH --}}
                    <div class="space-y-6 pt-6 border-t border-gray-100">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Perihal / Keterangan <span class="{{ $theme['text'] }}">*</span></label>
                            <textarea name="perihal" rows="2" placeholder="Misal: Peminjaman Ruangan GSG" class="w-full bg-gray-50 border border-gray-200 rounded-xl {{ $theme['ring'] }} text-xs md:text-sm py-3 px-4 shadow-sm" required></textarea>
                            <p class="text-[9px] text-gray-400 mt-1 font-medium italic uppercase tracking-wider">Bahasa singkat & jelas.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Penyelenggara <span class="{{ $theme['text'] }}">*</span></label>
                                <input type="text" name="penyelenggara" placeholder="Contoh: Panitia Syiar" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 text-xs md:text-sm {{ $theme['ring'] }} uppercase" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Nomor <span class="{{ $theme['text'] }}">*</span></label>
                                <input type="number" name="jumlah" value="1" min="1" max="20" class="w-full bg-gray-50 border border-gray-200 rounded-lg py-2 px-3 text-sm {{ $theme['ring'] }}" required>
                                <p class="text-[9px] text-gray-400 mt-1 italic">Maksimal 20 nomor sekali generate.</p>
                            </div>
                        </div>

                        <button type="submit" onclick="this.disabled=true; this.innerText='GENERATING...'; this.form.submit();" class="w-full {{ $theme['bg'] }} {{ $theme['hover'] }} text-white font-black py-4 rounded-xl shadow-md transition transform active:scale-95 flex justify-center items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed tracking-widest text-xs md:text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span class="hidden md:inline">GENERATE NOMOR SURAT SEKARANG</span>
                            <span class="md:hidden">GENERATE SEKARANG</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- KOLOM KANAN: DOWNLOAD & RIWAYAT --}}
        <div class="lg:col-span-1 space-y-8">
            
            {{-- HASIL GENERATE (Jika Ada) --}}
            @if(session('generated_list'))
            <div class="bg-white rounded-xl shadow-xl overflow-hidden border-2 {{ $theme['border'] }} animate-fade-in-down">
                <div class="{{ $theme['bg'] }} px-5 py-4 text-white">
                    <h3 class="font-bold text-base flex items-center gap-2 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Hasil Generator
                    </h3>
                    <div class="flex flex-col gap-2">
                        <button onclick="downloadCSV()" class="w-full bg-white {{ $theme['text'] }} hover:bg-gray-50 font-bold py-2.5 rounded-lg shadow text-sm transition">
                            Download Excel (.csv)
                        </button>
                    </div>
                </div>

                <div class="p-5 {{ $theme['light'] }} opacity-80">
                    <p class="text-[10px] font-bold {{ $theme['text'] }} uppercase tracking-widest text-center mb-2">Nomor Terakhir</p>
                    <div class="bg-white border-2 {{ $theme['border'] }} opacity-50 py-3 px-2 rounded-lg font-mono text-sm font-bold text-gray-800 select-all shadow-sm break-all text-center mb-4">
                        {{ session('last_number') }}
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <ul class="max-h-48 overflow-y-auto divide-y divide-gray-100">
                            @foreach(session('generated_list') as $nomor)
                                <li class="px-4 py-2 text-[10px] font-mono text-gray-600 flex justify-between items-center group hover:bg-gray-50">
                                    <span class="truncate w-10/12">{{ $nomor }}</span>
                                    <button onclick="navigator.clipboard.writeText('{{ $nomor }}'); alert('Nomor Disalin!')" class="text-gray-300 hover:{{ $theme['text'] }}">📋</button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif



            {{-- RIWAYAT LOKAL --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 animasi-kotak delay-100">
                <div class="bg-gray-800 px-5 py-3 flex justify-between items-center text-white">
                    <h3 class="font-bold text-xs uppercase">Riwayat Lokal</h3>
                    <span class="bg-gray-700 text-[10px] px-2 py-0.5 rounded-full">{{ $riwayat->count() }}</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                    @forelse($riwayat as $r)
                        <div class="p-4 hover:bg-gray-50 transition group">
                            <span class="font-mono text-[10px] font-bold text-emerald-600 block mb-1 cursor-pointer" onclick="navigator.clipboard.writeText('{{ $r->no_surat_full }}'); alert('Disalin!')">
                                {{ $r->no_surat_full }}
                            </span>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] text-gray-400 truncate w-32">{{ $r->perihal }}</span>
                                <span class="text-[8px] bg-gray-100 px-1 rounded text-gray-500 uppercase">{{ $r->jenis }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-xs italic">Belum ada riwayat surat.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-in; }
    .animate-fade-in-down { animation: fadeInDown 0.4s ease-out; }
</style>

<script>
    function updateForm() {
        const lingkup = document.getElementById('lingkup').value;
        const wrapFakultas = document.getElementById('wrapper-fakultas');
        const katPusat = document.getElementById('kategori_pusat');
        const katFakultas = document.getElementById('kategori_fakultas');
        const wrapDetails = document.getElementById('wrapper-details');

        wrapDetails.classList.add('hidden');
        katPusat.value = ""; katFakultas.value = "";

        if (lingkup === 'PUSAT') {
            wrapFakultas.classList.add('hidden');
            katPusat.classList.remove('hidden');
            katFakultas.classList.add('hidden');
        } else {
            wrapFakultas.classList.remove('hidden');
            katPusat.classList.add('hidden');
            katFakultas.classList.remove('hidden');
        }
    }

    function toggleDetails(scope) {
        const wrapDetails = document.getElementById('wrapper-details');
        const inAcara = document.getElementById('input-acara');
        const inDept = document.getElementById('input-dept');
        const inBidang = document.getElementById('input-bidang');
        
        let val = (scope === 'PUSAT') ? document.getElementById('kategori_pusat').value : document.getElementById('kategori_fakultas').value;

        wrapDetails.classList.remove('hidden');
        inAcara.classList.add('hidden'); inDept.classList.add('hidden'); inBidang.classList.add('hidden');

        if (val === 'PROKER_KM') {
            inAcara.classList.remove('hidden');
        } else if (val === 'PROKER_NON_KM') {
            if (scope === 'PUSAT') inDept.classList.remove('hidden'); 
            else inBidang.classList.remove('hidden'); 
        } else {
            wrapDetails.classList.add('hidden');
        }
    }

    function downloadCSV() {
        let data = @json(session('generated_list') ?? []);
        let csvContent = "data:text/csv;charset=utf-8,Nomor Surat\n";
        data.forEach(row => csvContent += row + "\n");
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "nomor_surat_alfath.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection