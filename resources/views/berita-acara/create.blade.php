@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-7xl mx-auto font-inter">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="border-l-4 {{ $theme['border'] }} pl-4">
            <h1 class="text-xl md:text-3xl font-black text-gray-900 tracking-tight uppercase">
                Berita Acara
            </h1>
            <p class="text-gray-500 text-[10px] md:text-sm mt-1 uppercase font-bold tracking-widest">
                LDK Al-Fath
            </p>
        </div>

        
    </div>

    {{-- ALERT SUKSES --}}
    



    {{-- FORM CONTAINER --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-12 animasi-kotak">
        <div class="{{ $theme['bg'] }} p-6 md:p-8 text-white">
            <h2 class="text-2xl md:text-3xl font-black tracking-tight uppercase">Formulir Digital</h2>
            <p class="text-white/80 mt-1 md:mt-2 font-bold text-[10px] md:text-sm uppercase tracking-widest">Auto-generate ke Google Docs.</p>
        </div>

        <form action="{{ route('berita-acara.store') }}" method="POST" class="p-6 md:p-10 space-y-10">
            @csrf
            {{-- Form input tetap sama seperti sebelumnya... --}}
            <div class="space-y-6">
                <h3 class="text-base md:text-lg font-black text-gray-800 border-b-2 border-red-100 pb-2 flex items-center uppercase tracking-tight">
                    <span class="bg-red-100 {{ $theme['text'] }} py-1 px-3 rounded-md text-xs font-black mr-3">A</span> 
                    Informasi Dasar
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Email Pembuat <span class="text-red-500">*</span></label>
                        <input type="email" name="email_pembuat" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 focus:border-red-500 transition shadow-sm text-xs md:text-sm py-2.5 px-4" placeholder="Email" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Departemen <span class="text-red-500">*</span></label>
                        <select name="asal_departemen" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 focus:border-red-500 transition shadow-sm text-xs md:text-sm py-2.5 px-4" required>
                            <option value="" disabled selected>-- Pilih --</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Penyelenggara <span class="text-red-500">*</span></label>
                        <input type="text" name="penyelenggara" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 focus:border-red-500 transition shadow-sm text-xs md:text-sm py-3 px-4 uppercase" placeholder="Panitia Pelaksana" required>
                    </div>
                </div>
            </div>

            {{-- B. DETAIL KEGIATAN --}}
            <div class="space-y-6">
                <h3 class="text-base md:text-lg font-black text-gray-800 border-b-2 border-red-100 pb-2 flex items-center uppercase tracking-tight">
                    <span class="bg-red-100 {{ $theme['text'] }} py-1 px-3 rounded-md text-xs font-black mr-3">B</span> 
                    Detail Kegiatan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Nama Kegiatan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kegiatan" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-lg py-3 px-4 focus:ring-2 focus:ring-red-800 text-xs md:text-sm uppercase" placeholder="Masukkan nama lengkap kegiatan" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_kegiatan" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-lg py-3 px-4 focus:ring-2 focus:ring-red-800 text-xs md:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_selesai" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-lg py-3 px-4 focus:ring-2 focus:ring-red-800 text-xs md:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Kota Pelaksanaan</label>
                        <input type="text" name="kota" value="Bandung" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-lg py-3 px-4 focus:ring-2 focus:ring-red-800 text-xs md:text-sm uppercase" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Jml Peserta (Org)</label>
                        <input type="number" name="jumlah_peserta" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-lg py-3 px-4 focus:ring-2 focus:ring-red-800 text-xs md:text-sm" placeholder="0" required>
                    </div>
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Tempat Pelaksanaan <span class="text-red-500">*</span></label>
                        <input type="text" name="tempat" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-3 px-4 uppercase" placeholder="GSG / Zoom" required>
                    </div>
                </div>
            </div>

            {{-- C. ISI & DOKUMENTASI --}}
            <div class="space-y-6">
                <h3 class="text-base md:text-lg font-black text-gray-800 border-b-2 border-red-100 pb-2 flex items-center uppercase tracking-tight">
                    <span class="bg-red-100 {{ $theme['text'] }} py-1 px-3 rounded-md text-xs font-black mr-3">C</span> 
                    Isi & Dokumentasi
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Tautan Rundown <span class="text-red-500">*</span></label>
                        <input type="url" name="rangkaian_kegiatan" class="w-full bg-white border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-3 px-4" placeholder="Link Drive/Spreadsheet" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Tautan Dokumentasi <span class="text-red-500">*</span></label>
                        <input type="url" name="link_dokumentasi" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-3 px-4" placeholder="Link folder dokumentasi" required>
                        <p class="text-[9px] text-emerald-600 mt-2 font-bold italic uppercase tracking-wider">💡 Pastikan akses "Anyone with the link".</p>
                    </div>
                </div>
            </div>

            {{-- D. TANDA TANGAN --}}
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                <h3 class="text-base md:text-lg font-black text-gray-800 mb-6 flex items-center uppercase tracking-tight">
                    <span class="bg-red-100 {{ $theme['text'] }} py-1 px-3 rounded-md text-xs font-black mr-3">D</span> 
                    Penanda Tangan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:border-red-300 transition duration-300">
                        <h4 class="font-bold text-red-700 border-b border-gray-100 pb-2 mb-4">Ketua Pelaksana</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ketua" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 focus:border-red-500 transition shadow-sm text-xs md:text-sm py-2.5 px-4 uppercase" placeholder="Nama Ketua" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">NIM Ketua <span class="text-red-500">*</span></label>
                                <input type="text" name="nim_ketua" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-2.5 px-4 uppercase" placeholder="NIM" required>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                        <h4 class="font-bold text-red-700 border-b border-gray-100 pb-2 mb-4">Sekretaris Pelaksana</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Nama Sekretaris <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_sekretaris" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-2.5 px-4 uppercase" placeholder="Nama Sekretaris" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">NIM Sekretaris <span class="text-red-500">*</span></label>
                                <input type="text" name="nim_sekretaris" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-2.5 px-4 uppercase" placeholder="NIM" required>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                        <h4 class="font-bold text-red-700 border-b border-gray-100 pb-2 mb-4">Koordinator / Mengetahui</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">Nama Koordinator (Kadept/Masul Fkt) <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_koordinator" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-2.5 px-4 uppercase" placeholder="Nama Koordinator" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-700 uppercase mb-1.5 ml-1 tracking-widest">NIM Koordinator <span class="text-red-500">*</span></label>
                                <input type="text" name="nim_koordinator" class="w-full bg-gray-50 border-gray-300 text-gray-900 rounded-xl focus:ring-2 focus:ring-red-800 text-xs md:text-sm py-2.5 px-4 uppercase" placeholder="NIM" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between pt-6 border-t border-gray-100 gap-4">
                <a href="https://drive.google.com/drive/folders/1NmzZTkQFgM6XTowlctmY3Vxo0IUxOTXE" target="_blank" class="inline-flex items-center px-6 py-3 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 rounded-lg text-sm font-bold transition shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    Buka Google Drive 📂
                </a>
                
                <div class="flex items-center gap-2">
                    <button type="reset" class="text-gray-400 hover:text-gray-600 font-bold px-6 py-3 text-sm transition">Reset Form</button>
                    <button type="submit" onclick="this.disabled=true; this.innerText='PROSES...'; this.form.submit();" class="bg-red-700 hover:bg-red-800 text-white font-black py-3 px-6 md:px-10 rounded-xl shadow-xl shadow-red-900/20 transition transform active:scale-95 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed text-xs md:text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="hidden md:inline">GENERATE DOKUMEN</span>
                        <span class="md:hidden">GENERATE</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="mt-12 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden animasi-kotak delay-100">
        <div class="bg-gray-800 p-4 text-white flex justify-between items-center">
            <h3 class="font-bold flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat Pembuatan Berita Acara
            </h3>
            <span class="text-xs bg-gray-700 px-3 py-1 rounded-full text-gray-300">Data Terbaru</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4">Waktu Input</th>
                        <th class="px-6 py-4">Departemen</th>
                        <th class="px-6 py-4">Nama Kegiatan</th>
                        <th class="px-6 py-4">Ketua Pelaksana</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayatCloud as $row)
                    <tr class="bg-white hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                            {{ $row->waktu_input ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-red-50 text-red-700 rounded text-[10px] font-bold uppercase tracking-wider">
                                {{ $row->departemen ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 uppercase">
                            {{ $row->nama_kegiatan ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900 font-semibold text-xs">{{ $row->ketua ?? '-' }}</div>
                            <div class="text-[10px] text-gray-400 italic">{{ $row->email_pembuat ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($row->link_dokumen)
                                <a href="{{ $row->link_dokumen }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-md text-xs font-bold transition duration-200">
                                    Buka Doc ↗
                                </a>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4 opacity-40">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <p class="text-xs font-black uppercase tracking-widest">Database Cloud Kosong</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection