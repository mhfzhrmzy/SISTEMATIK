<!-- GURU MANAGEMENT VIEW -->
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Kelola Akun Guru</h1>
            <p class="text-xs text-slate-500 mt-1">Tambah, perbarui, atau hapus data akun guru pengampu.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.template') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 shadow-xs flex items-center gap-1.5 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Template CSV
            </a>
            <button type="button" onclick="openImportGuruModal()" 
                    style="background-color: #7e22ce; color: #ffffff;"
                    class="bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import CSV Guru
            </button>
            <span class="rounded-full bg-blue-100 text-blue-700 px-3.5 py-1 text-xs font-bold shadow-xs">
                {{ $gurus->count() }} Guru Terdaftar
            </span>
        </div>
    </div>

    <!-- TAMBAH GURU FORM -->
    <div class="mb-6 bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.42a12.1 12.1 0 01.67 6.48A12 12 0 0112 20.06a12 12 0 01-6.83-3 12.1 12.1 0 01.67-6.48L12 14z"></path>
                </svg>
            Tambah Akun Guru Baru
        </h2>
        <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @csrf
            <input type="hidden" name="from" value="superadmin">
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">NIP (18 Digit)</label>
                <input type="text" name="nip" value="{{ old('nip') }}" maxlength="18" inputmode="numeric" pattern="[0-9]*" required 
                       placeholder="Masukkan NIP 18 digit"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required 
                       placeholder="Masukkan Nama Lengkap"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Password</label>
                <input type="password" name="password" required 
                       placeholder="Minimal 6 karakter"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Foto Profil <span class="font-normal text-slate-400">(opsional)</span></label>
                <input type="file" name="foto_profile" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
            </div>
            <div class="flex items-center gap-2 md:col-span-2 md:justify-end flex-wrap">
                <button type="submit" class="w-full md:w-auto rounded-xl bg-[#6D1837] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-amber-950/20 hover:bg-[#b21a3b] transition cursor-pointer">
                    + Tambah Akun Guru
                </button>
            </div>
        </form>
    </div>

    <!-- DAFTAR GURU TABLE -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-slate-900">Daftar Akun Guru Registered</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-700 uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 rounded-l-xl">Foto</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3 text-right rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($gurus as $guru)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                @if ($guru->foto_profile)
                                    <img src="{{ asset('storage/' . $guru->foto_profile) }}" alt="{{ $guru->nama_lengkap }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-xs shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-800 font-bold flex items-center justify-center text-xs shrink-0 border border-purple-200/80 shadow-xs">
                                        {{ strtoupper(substr($guru->nama_lengkap, 0, 2)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-normal text-slate-500 font-mono tracking-tight">{{ $guru->nip }}</td>
                            <td class="px-4 py-3 font-normal text-slate-700">{{ $guru->nama_lengkap }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button"
                                            onclick="openEditGuruModal('{{ $guru->nip }}', '{{ addslashes($guru->nama_lengkap) }}')"
                                            class="text-amber-600 font-medium hover:underline cursor-pointer">Edit</button>
                                    <form action="{{ route('admin.destroy', $guru) }}" method="POST" onsubmit="return confirm('Hapus akun guru ini?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="from" value="superadmin">
                                        <button type="submit" class="text-red-600 font-medium hover:underline cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-medium">Belum ada akun guru registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
