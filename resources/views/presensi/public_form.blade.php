<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi - LDK Al-Fath</title>
    <link rel="icon" href="{{ asset('img/LogoPusat.png') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#fcfcfc] flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md border-t-[10px] border-red-800 relative overflow-hidden">
        {{-- Dekorasi Sudut --}}
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-50 rounded-full opacity-50"></div>

        <div class="text-center mb-8 relative">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Presensi Kegiatan</h1>
            <div class="h-1.5 w-12 bg-red-600 mx-auto mt-2 rounded-full"></div>
            
            <div class="mt-6 bg-red-50/50 p-4 rounded-2xl border border-red-100">
                <p class="text-red-800 font-extrabold text-sm uppercase">{{ $sesi->nama_kegiatan }}</p>
                <p class="text-gray-400 text-[9px] mt-1 uppercase tracking-[0.2em] font-bold">{{ $sesi->kategori }}</p>
            </div>
        </div>

        <form action="{{ route('presensi.submit', $sesi->id_sesi) }}" method="POST" class="space-y-4 relative">
            @csrf
            <input type="hidden" name="submission_id" value="{{ request()->query('submission_id') }}">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">NIM (12 Digit)</label>
                <input type="text" name="nim" required maxlength="12" 
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12)"
                    placeholder="120XXXXXXXXX"
                    class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-red-800 focus:border-transparent transition-all outline-none text-sm font-mono font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Email</label>
                    <input type="email" name="email" required placeholder="fulan@mail.com"
                        class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Amanah</label>
                    <input type="text" name="amanah" required placeholder="Staff/BPH"
                        class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-bold uppercase">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="SESUAI IDENTITAS"
                    class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm font-black uppercase text-gray-800">
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Asal Wajihah / Unit</label>
                <div class="relative">
                    <select name="asal_wajihah" required 
                        class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-red-800 transition-all outline-none text-[11px] font-bold appearance-none cursor-pointer uppercase">
                        <option value="" disabled selected>-- Pilih Wajihah --</option>
                        @foreach($allUnits as $unit)
                            <option value="{{ $unit }}">{{ $unit }}</option>
                        @endforeach
                        <option value="Umum/Non-Wajihah">Umum / Non-Wajihah</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-4 top-6 text-red-800 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest mb-2 block">Status Kehadiran</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['Hadir', 'Izin', 'Sakit'] as $status)
                        <label class="relative flex items-center justify-center p-3.5 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all has-[:checked]:border-red-800 has-[:checked]:bg-red-50 group">
                            <input type="radio" name="status_kehadiran" value="{{ $status }}" required class="hidden">
                            <span class="text-[11px] font-extrabold text-gray-500 group-has-[:checked]:text-red-800 uppercase tracking-tighter">{{ $status }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" onclick="this.disabled=true; this.innerText='MENGIRIM DATA...'; this.form.submit();"
                class="w-full bg-red-800 hover:bg-red-900 text-white font-black py-4 rounded-2xl shadow-xl shadow-red-900/10 transition-all active:scale-[0.98] tracking-[0.2em] text-xs mt-4 uppercase disabled:opacity-50 disabled:cursor-not-allowed">
                Submit Kehadiran
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-50 flex flex-col items-center">
            <p class="text-[9px] text-gray-300 uppercase font-bold tracking-[0.3em]">SIAP DAKWAH • BIRO KESEKRETARIATAN LDK AL-FATH</p>
        </div>
    </div>
</body>
</html>