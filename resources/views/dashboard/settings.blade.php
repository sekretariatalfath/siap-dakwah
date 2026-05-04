@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 md:mb-10">
        <h1 class="text-xl md:text-3xl font-black text-gray-900 tracking-tight leading-none uppercase">Pengaturan Akun</h1>
        <p class="text-[10px] md:text-sm text-gray-500 mt-2 font-bold uppercase tracking-widest">Konfigurasi Sistem SIAP Dakwah</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- KIRI: Ganti Password Saya --}}
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-12 -mt-12 opacity-50 group-hover:scale-110 transition duration-500"></div>
                
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2 relative z-10">
                    <span class="w-2 h-5 bg-red-700 rounded-full"></span>
                    Keamanan Akun
                </h3>

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
            </div>

            {{-- DANGER ZONE: Reset SPS --}}
            @if(Auth::user()->role == 'superadmin' || Auth::user()->unit == 'Kestari')
            <div class="bg-amber-50 p-8 rounded-[40px] border border-amber-100 mb-6">
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
            <div class="bg-red-50 p-8 rounded-[40px] border border-red-100">
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
            <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-5 bg-gray-900 rounded-full"></span>
                        Daftar Pengguna Sistem
                    </h3>
                    <button onclick="document.getElementById('modalRegister').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition shadow-lg shadow-emerald-100">
                        Tambah User Baru
                    </button>
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
@endsection
