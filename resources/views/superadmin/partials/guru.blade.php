<!-- GURU MANAGEMENT VIEW -->
<div>
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Kelola Akun Guru</h1>
            <p class="text-xs text-slate-500 mt-1">Tambah, perbarui, atau hapus data akun guru pengampu.</p>
        </div>
        <span class="rounded-full bg-blue-100 text-blue-700 px-3.5 py-1 text-xs font-bold shadow-xs">
            {{ $gurus->count() }} Akun Registered
        </span>
    </div>

    <!-- TAMBAH GURU FORM -->
    <div class="mb-6 bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-slate-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#6D1837]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
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
            <div class="flex items-end md:justify-end">
                <button type="submit" class="w-full md:w-auto rounded-xl bg-[#4a101d] px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-amber-950/20 hover:bg-[#380b15] transition cursor-pointer">
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
