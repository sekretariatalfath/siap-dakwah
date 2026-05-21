@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-7xl mx-auto font-inter">

    {{-- HEADER --}}
    <div class="mb-6 md:mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-xl md:text-3xl font-black text-gray-900 tracking-tight uppercase">Surat Masuk</h1>
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-4">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-700 text-xs font-bold">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <p class="mt-2 text-sm text-red-600 font-bold">LDK Al-Fath Telkom University</p>
            <div class="w-16 h-1 {{ $theme['bg'] }} mt-2 rounded-full"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        
        {{-- FORM INPUT (KIRI) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden sticky top-6 animasi-kotak">
                <div class="{{ $theme['bg'] }} px-6 py-4 flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="font-bold text-white text-lg">Arsip Surat Baru</h3>
                </div>
                
                <form action="{{ route('surat-masuk.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor Surat Asli <span class="text-red-500">*</span></label>
                        <input type="text" name="no_surat" value="{{ old('no_surat') }}" placeholder="Contoh: 001/A/ALFATH/X/2026" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm {{ $theme['ring'] }} py-2.5 @error('no_surat') border-red-500 @enderror" required>
                        @error('no_surat') <p class="text-red-600 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pengirim / Instansi <span class="text-red-500">*</span></label>
                        <input type="text" name="pengirim" value="{{ old('pengirim') }}" placeholder="Sebutkan Nama Instansi Pengirim" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm {{ $theme['ring'] }} py-2.5 uppercase" required>
                    </div>

                    {{-- KONTAK DINAMIS --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Kontak Pengirim <span class="text-red-500">*</span></label>
                        
                        <select name="jenis_kontak" id="jenis_kontak" onchange="toggleKontak()" class="w-full bg-white border border-gray-200 rounded-lg text-sm mb-3 cursor-pointer {{ $theme['ring'] }} py-2.5" required>
                            <option value="" disabled {{ old('jenis_kontak') == '' ? 'selected' : '' }}>-- Pilih Jenis Kontak --</option>
                            <option value="WHATSAPP" {{ old('jenis_kontak') == 'WHATSAPP' ? 'selected' : '' }}>WhatsApp / Telepon</option>
                            <option value="EMAIL" {{ old('jenis_kontak') == 'EMAIL' ? 'selected' : '' }}>Email</option>
                            <option value="KEDUANYA" {{ old('jenis_kontak') == 'KEDUANYA' ? 'selected' : '' }}>Keduanya (WA & Email)</option>
                            <option value="TIDAK ADA" {{ old('jenis_kontak') == 'TIDAK ADA' ? 'selected' : '' }}>Tidak Ada</option>
                        </select>

                        <div id="box-wa" class="{{ in_array(old('jenis_kontak'), ['WHATSAPP', 'KEDUANYA']) ? '' : 'hidden' }} mb-3">
                            <input type="number" name="input_wa" value="{{ old('input_wa') }}" placeholder="Nomor WhatsApp (Misal: 0812xxxxxxxx)" class="w-full border border-gray-200 rounded text-sm bg-white {{ $theme['ring'] }} py-2">
                        </div>
                        
                        <div id="box-email" class="{{ in_array(old('jenis_kontak'), ['EMAIL', 'KEDUANYA']) ? '' : 'hidden' }}">
                            <input type="email" name="input_email" value="{{ old('input_email') }}" placeholder="Alamat Email Aktif" class="w-full border border-gray-200 rounded text-sm bg-white {{ $theme['ring'] }} py-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Kegiatan (Opsional)</label>
                        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" placeholder="Sebutkan Nama Kegiatan Jika Ada" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm {{ $theme['ring'] }} py-2.5 uppercase">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Perihal Surat <span class="text-red-500">*</span></label>
                        <textarea name="perihal" placeholder="Jelaskan secara ringkas maksud surat yang diterima..." rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm {{ $theme['ring'] }} @error('perihal') border-red-500 @enderror" required>{{ old('perihal') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Ditujukan Kepada <span class="text-red-500">*</span></label>
                            <input type="text" name="ditujukan_kepada" value="{{ old('ditujukan_kepada', 'LDK Al-Fath') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2 uppercase" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tanggal Terima <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_terima" value="{{ old('tgl_terima', date('Y-m-d')) }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs py-2" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Penerima Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="penerima_fisik" value="{{ old('penerima_fisik', Auth::user()->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm py-2.5 uppercase" required>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <label class="block text-xs font-bold text-blue-800 uppercase mb-1">Tautan (Link) Google Drive <span class="text-red-500">*</span></label>
                        <input type="url" name="link_drive" value="{{ old('link_drive') }}" placeholder="Tempelkan Link Folder atau File Hasil Scan Surat" class="w-full bg-white border border-blue-300 rounded-lg text-sm focus:ring-blue-500 py-2.5" required>
                        <p class="text-[9px] text-blue-600 mt-2 font-bold italic">💡 Tips: Pastikan akses file di Drive sudah diatur ke "Anyone with the link".</p>
                        @error('link_drive') <p class="text-red-600 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" onclick="this.disabled=true; this.innerText='SIMPAN...'; this.form.submit();" class="w-full {{ $theme['bg'] }} {{ $theme['hover'] }} text-white font-bold py-3 rounded-xl shadow transition transform active:scale-95 text-xs md:text-sm flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="hidden md:inline">Simpan Arsip Surat Masuk</span>
                        <span class="md:hidden">Simpan Arsip</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- TABEL DATA (KANAN) --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden animasi-kotak delay-100">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-700 uppercase text-sm tracking-wider">Database Arsip Surat</h3>
                    <span class="{{ $theme['light'] }} {{ $theme['text'] }} text-[10px] px-2 py-1 rounded-full font-black">{{ $suratMasuk->count() }} DATA</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left min-w-[600px] md:min-w-full">
                        <thead class="text-[10px] text-gray-500 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3">Penerimaan</th>
                                <th class="px-4 py-3">Pengirim & Perihal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($suratMasuk as $surat)
                            <tr class="transition {{ $surat->is_checked ? 'bg-emerald-50' : 'hover:bg-gray-50' }}">
                                <td class="px-4 py-4 whitespace-nowrap text-gray-500 text-xs align-top">
                                    <div class="font-bold text-gray-700">{{ $surat->tgl_terima->format('d/m/Y') }}</div>
                                    @if($surat->is_checked)
                                        <div class="mt-2 text-[8px] font-black text-emerald-600 bg-white border border-emerald-300 px-2 py-0.5 rounded-full w-fit">
                                            ✔ VERIFIED
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="font-black text-gray-900 leading-tight">{{ $surat->pengirim }}</div>
                                    <div class="text-[9px] text-gray-400 mt-0.5">No: {{ $surat->no_surat }}</div>
                                    <div class="text-xs text-gray-600 mt-2 font-medium italic">"{{ Str::limit($surat->perihal, 70) }}"</div>
                                    <div class="mt-3 flex gap-2">
                                        <span class="text-[9px] bg-gray-200 px-2 py-0.5 rounded text-gray-600 font-bold uppercase tracking-tighter">Yth. {{ $surat->ditujukan_kepada }}</span>
                                        @if($surat->nama_kegiatan)
                                            <span class="text-[9px] bg-blue-100 px-2 py-0.5 rounded text-blue-700 font-bold uppercase tracking-tighter">Event: {{ $surat->nama_kegiatan }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center align-top space-y-3">
                                    {{-- LINK DRIVE --}}
                                    <a href="{{ $surat->link_drive }}" target="_blank" class="flex items-center justify-center w-8 h-8 mx-auto rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition" title="Buka File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>

                                    @if(Auth::user()->role == 'superadmin')
                                        {{-- TOGGLE CHECK --}}
                                        <form action="{{ route('surat-masuk.toggle', $surat->no_surat) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="flex items-center justify-center w-8 h-8 mx-auto rounded-lg {{ $surat->is_checked ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400' }} hover:scale-110 transition shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>

                                        {{-- DELETE --}}
                                        <form action="{{ route('surat-masuk.destroy', $surat->no_surat) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center justify-center w-8 h-8 mx-auto rounded-lg bg-red-50 text-red-300 hover:text-red-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-32 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-6">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-gray-100 rounded-full blur-2xl opacity-50 animate-pulse"></div>
                                            <div class="relative bg-white p-8 rounded-[32px] border-2 border-gray-50 shadow-xl">
                                                <svg class="w-16 h-16 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">Kotak Masuk Kosong</h3>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Belum ada arsip surat masuk yang diterima.</p>
                                        </div>
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
</div>

<script>
    function toggleKontak() {
        const val = document.getElementById('jenis_kontak').value;
        const wa = document.getElementById('box-wa');
        const email = document.getElementById('box-email');
        const inputWa = document.querySelector('input[name="input_wa"]');
        const inputEmail = document.querySelector('input[name="input_email"]');

        wa.classList.add('hidden');
        email.classList.add('hidden');
        inputWa.required = false;
        inputEmail.required = false;

        if (val === 'WHATSAPP') {
            wa.classList.remove('hidden');
            inputWa.required = true; 
        } else if (val === 'EMAIL') {
            email.classList.remove('hidden');
            inputEmail.required = true; 
        } else if (val === 'KEDUANYA') {
            wa.classList.remove('hidden');
            email.classList.remove('hidden');
            inputWa.required = true; 
            inputEmail.required = true; 
        }
    }
</script>
@endsection