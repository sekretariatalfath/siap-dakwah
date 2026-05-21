@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 md:mb-10">
        <h1 class="text-xl md:text-3xl font-black text-gray-900 tracking-tight leading-none uppercase">Pengaturan Akun</h1>
        <p class="text-[10px] md:text-sm text-gray-500 mt-2 font-bold uppercase tracking-widest">Konfigurasi Sistem SIAP Dakwah</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- KIRI: Ganti Password Saya & Saklar --}}
        <div class="space-y-6">
            {{-- SAKLAR OTENTIKASI (Hanya Superadmin) --}}
            @if(Auth::user()->role == 'superadmin')
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100 relative overflow-hidden group animasi-kotak">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition duration-500"></div>
                
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center gap-2 relative z-10">
                    <span class="w-2 h-5 bg-indigo-700 rounded-full"></span>
                    Mode Otentikasi
                </h3>

                <form action="{{ route('settings.toggle-auth') }}" method="POST" class="relative z-10 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                        <div>
                            <p class="text-[11px] font-black text-gray-700 uppercase">Sumber Akun</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">{{ $authSource }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="source" value="database" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ $authSource === 'database' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
                                Database
                            </button>
                            <button type="submit" name="source" value="hardcode" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ $authSource === 'hardcode' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
                                Hardcode
                            </button>
                        </div>
                    </div>
                    <p class="text-[9px] text-gray-400 font-medium leading-relaxed">
                        *Mode **Hardcode** bikin login super cepat & anti-error database, tapi fitur ganti password & tambah user bakal dikunci.
                    </p>
                </form>
            </div>
            @endif

            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100 relative overflow-hidden group animasi-kotak delay-100">
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition duration-500"></div>
                
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2 relative z-10">
                    <span class="w-2 h-5 bg-red-700 rounded-full"></span>
                    Keamanan Akun
                </h3>

                @if($authSource === 'hardcode')
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-center relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">🔒 Fitur Terkunci</p>
                    <p class="text-[9px] text-gray-400 font-medium mt-1">Ganti password dinonaktifkan di mode Hardcode.</p>
                </div>
                @else
                <form action="{{ route('settings.password') }}" method="POST" class="space-y-4 relative z-10">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Password Lama</label>
                        <input type="password" name="current_password" required class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Password Baru</label>
                        <input type="password" name="new_password" required class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Konfirmasi Password</label>
                        <input type="password" name="new_password_confirmation" required class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-red-800 transition-all outline-none text-sm">
                    </div>
                    <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-red-800 transition-all active:scale-95">
                        <span class="hidden md:inline">Update Password Akun</span>
                        <span class="md:hidden">Update Password</span>
                    </button>
                </form>
                @endif
            </div>

            {{-- DANGER ZONE: Reset SPS --}}
            {{-- MANAJEMEN KONTAK PERSON (FOOTER) --}}
            @if(Auth::user()->role == 'superadmin' || Auth::user()->unit == 'Kestari')
            <div class="bg-indigo-50/50 p-8 rounded-[40px] border border-indigo-100 mb-6 animasi-kotak delay-150">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-black text-indigo-900 uppercase tracking-widest flex items-center gap-2">
                        📞 Kontak Person (Footer)
                    </h3>
                    <button onclick="document.getElementById('modalTambahCp').classList.remove('hidden')" class="px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-indigo-700 transition duration-300 shadow-lg shadow-indigo-200 active:scale-95">
                        Tambah CP
                    </button>
                </div>
                
                @php
                    $hasCp = false;
                    foreach(['PUSAT', 'FAKULTAS'] as $cat) {
                        if(isset($contactPersons[$cat]) && count($contactPersons[$cat]) > 0) {
                            $hasCp = true;
                        }
                    }
                @endphp

                @if(!$hasCp)
                    <div class="text-center py-8 bg-white/40 border border-dashed border-indigo-200 rounded-3xl p-6">
                        <p class="text-3xl mb-2">💬</p>
                        <p class="text-[10px] text-indigo-900/60 font-black uppercase tracking-wider">Belum ada kontak terdaftar</p>
                        <p class="text-[9px] text-indigo-500/50 mt-1 font-bold">Silakan tambahkan Kontak Person Pusat atau Fakultas melalui tombol di atas.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach(['PUSAT', 'FAKULTAS'] as $kategori)
                            @if(isset($contactPersons[$kategori]) && count($contactPersons[$kategori]) > 0)
                                <div>
                                    <span class="text-[9px] font-black text-indigo-800 bg-indigo-100/80 px-3 py-1 rounded-full uppercase tracking-wider">{{ $kategori }}</span>
                                    <div class="mt-3 space-y-2.5">
                                        @foreach($contactPersons[$kategori] as $cp)
                                            <div class="flex justify-between items-center bg-white p-3.5 rounded-2xl border border-indigo-100 shadow-sm hover:border-indigo-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 text-[11px] font-black uppercase shadow-inner">
                                                        {{ strtoupper(substr($cp['nama'], 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-[11px] font-black text-gray-900 leading-tight">{{ $cp['nama'] }}</p>
                                                        <div class="flex items-center gap-1.5 mt-0.5">
                                                            <svg class="w-3 h-3 text-emerald-500 fill-current" viewBox="0 0 24 24">
                                                                <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.209-1.367a9.923 9.923 0 0 0 4.798 1.233h.005c5.505 0 9.99-4.477 9.99-9.983a9.957 9.957 0 0 0-2.927-7.06A9.963 9.963 0 0 0 12.012 2zm5.827 14.195c-.24.675-1.4 1.272-1.928 1.332-.479.055-.99.278-3.093-.548-2.529-.993-4.148-3.566-4.275-3.733-.126-.168-1.018-1.354-1.018-2.578 0-1.224.634-1.827.859-2.073.226-.247.49-.308.653-.308.163 0 .326.002.467.009.146.007.34-.055.534.408.2.477.674 1.644.733 1.764.06.12.1.26.02.42-.08.16-.12.26-.24.4-.12.14-.25.312-.358.42-.119.117-.243.245-.104.482.138.238.614 1.01 1.314 1.633.902.802 1.66 1.05 1.895 1.168.236.118.373.1.512-.06.14-.163.6-1.002.76-1.344.16-.34.32-.284.54-.202.22.082 1.393.656 1.632.776.24.12.4.18.458.283.06.103.06.6-.18 1.275z"/>
                                                            </svg>
                                                            <span class="text-[9px] text-gray-500 font-bold tracking-wider">+{{ $cp['wa'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form action="{{ route('settings.destroy-cp', $cp['row_index']) }}" method="POST" onsubmit="return confirm('Hapus Kontak Person ini dari website dan Google Sheets?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 opacity-40 md:opacity-0 group-hover:opacity-100 focus:opacity-100 active:scale-90">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-amber-50 p-8 rounded-[40px] border border-amber-100 mb-6 animasi-kotak delay-200">
                <h3 class="text-sm font-black text-amber-900 uppercase tracking-widest mb-2 flex items-center gap-2">
                    🧹 Reset Resources
                </h3>
                <p class="text-[10px] text-amber-700/70 font-medium leading-relaxed mb-6">
                    Hapus isi tab **Templates, Pedoman, & Informasi** saja. Akun & arsip surat tetap aman.
                </p>
                <form action="{{ route('settings.purge-sps') }}" method="POST" onsubmit="return confirm('Hapus data library & resource?')">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-amber-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-amber-200 hover:bg-amber-700 transition-all">
                        Reset Library
                    </button>
                </form>
            </div>
            @endif

            {{-- MASTER RESET: Full System --}}
            @if(Auth::user()->role == 'superadmin')
            <div class="bg-red-50 p-8 rounded-[40px] border border-red-100 animasi-kotak delay-300">
                <h3 class="text-sm font-black text-red-900 uppercase tracking-widest mb-2 flex items-center gap-2">
                    💀 Master System Reset
                </h3>
                <p class="text-[10px] text-red-700/70 font-medium leading-relaxed mb-6">
                    **TINDAKAN KRITIS.** Menghapus SELURUH data transaksi (Surat, Notulensi, Proposal, Presensi) baik di database lokal maupun Google Sheets. Sistem akan kembali seperti baru.
                </p>
                <form action="{{ route('settings.hard-reset') }}" method="POST" onsubmit="return confirm('PERINGATAN TERAKHIR: Anda akan menghapus SELURUH database organisasi. Ketik OK untuk melanjutkan.')">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-red-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-red-200 hover:bg-red-900 transition-all">
                        Hard Reset System
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- KANAN: Manajemen User --}}
        <div class="lg:col-span-2 space-y-6">
            @if(Auth::user()->role == 'superadmin')
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100 animasi-kotak delay-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-5 bg-gray-900 rounded-full"></span>
                        Daftar Pengguna Sistem
                    </h3>
                    @if($authSource === 'hardcode')
                    <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-[9px] font-black uppercase tracking-widest">
                        Terkunci
                    </span>
                    @else
                    <button onclick="document.getElementById('modalRegister').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition shadow-lg shadow-emerald-100">
                        Tambah User Baru
                    </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <th class="pb-4">Nama & Akun</th>
                                <th class="pb-4">Unit / Departemen</th>
                                <th class="pb-4">Role</th>
                                <th class="pb-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($users as $user)
                            <tr class="group">
                                <td class="py-5">
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $user->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">{{ $user->email }}</p>
                                </td>
                                <td class="py-5">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $user->unit }}</span>
                                </td>
                                <td class="py-5">
                                    <span class="px-2 py-1 rounded-md text-[8px] font-black uppercase {{ $user->role == 'superadmin' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-gray-50 text-gray-600 border border-gray-100' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($authSource === 'hardcode')
                                        <span class="text-[9px] font-bold text-gray-300 uppercase">No Action</span>
                                        @else
                                        {{-- Modal Reset Password Button --}}
                                        <button onclick="openResetModal('{{ $user->id }}', '{{ $user->name }}')" class="p-2 text-gray-400 hover:text-red-700 transition-all opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </button>
                                        @if($user->id != Auth::id())
                                        <form action="{{ route('settings.delete-user', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-700 transition-all opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL REGISTER --}}
<div id="modalRegister" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-md shadow-2xl border border-gray-100">
        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-2">Daftarkan Akun Baru</h3>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-8">Hanya untuk Pengurus / Fungsionaris Resmi</p>
        
        <form action="{{ route('settings.register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-gray-900 transition-all outline-none text-sm font-bold">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Email (Login)</label>
                <input type="email" name="email" required class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-gray-900 transition-all outline-none text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Password</label>
                    <input type="password" name="password" required class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-gray-900 transition-all outline-none text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Role</label>
                    <select name="role" class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-gray-900 transition-all outline-none text-sm font-bold">
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Nama Unit / Bidang</label>
                <input type="text" name="unit" required placeholder="Contoh: Biro Kesekretariatan" class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-gray-900 transition-all outline-none text-sm">
            </div>
            
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('modalRegister').classList.add('hidden')" class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-emerald-700 transition-all">
                    <span class="hidden md:inline">Simpan Akun Baru</span>
                    <span class="md:hidden">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RESET PASSWORD USER --}}
<div id="modalReset" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-md shadow-2xl border border-gray-100">
        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-2">Reset Password User</h3>
        <p id="resetUserName" class="text-[10px] text-red-700 font-bold uppercase tracking-widest mb-8"></p>
        
        <form id="formReset" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Password Baru</label>
                <input type="password" name="new_password" required minlength="6" class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-red-700 transition-all outline-none text-sm">
            </div>
            
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('modalReset').classList.add('hidden')" class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-red-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-red-800 transition-all">Update Password</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResetModal(id, name) {
        document.getElementById('resetUserName').innerText = "Target: " + name;
        document.getElementById('formReset').action = "/dashboard/settings/reset-user/" + id;
        document.getElementById('modalReset').classList.remove('hidden');
    }
</script>

{{-- MODAL TAMBAH CP --}}
<div id="modalTambahCp" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[40px] p-10 w-full max-w-md shadow-2xl border border-gray-100">
        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight mb-2">Tambah Kontak Person</h3>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-8">Otomatis sinkron dengan Google Sheets</p>
        
        <form action="{{ route('settings.store-cp') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Kategori Kontak</label>
                <select name="kategori" required class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-indigo-600 transition-all outline-none text-sm font-bold">
                    <option value="PUSAT">Pusat Bantuan Utama</option>
                    <option value="FAKULTAS">Bantuan Fakultas</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Nama Lengkap / Jabatan</label>
                <input type="text" name="nama" required placeholder="Contoh: Naufal (Admin)" class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-indigo-600 transition-all outline-none text-sm font-bold">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1 tracking-widest">Nomor WhatsApp</label>
                <input type="text" name="wa" required placeholder="Contoh: 628123456789" class="w-full mt-1.5 p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-indigo-600 transition-all outline-none text-sm font-bold">
                <p class="text-[9px] text-gray-400 mt-2 ml-1">* Awali dengan 62 tanpa tanda plus (+).</p>
            </div>
            
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('modalTambahCp').classList.add('hidden')" class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:bg-gray-50 rounded-2xl transition">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-indigo-700 transition-all">Simpan CP</button>
            </div>
        </form>
    </div>
</div>
@endsection
