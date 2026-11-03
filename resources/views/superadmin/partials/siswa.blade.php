<!-- SISWA MANAGEMENT VIEW -->
<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Kelola Akun Siswa</h1>
            <p class="text-xs text-slate-500 mt-1">Tambah, perbarui, atau hapus data akun siswa.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('siswa.template') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 shadow-xs flex items-center gap-1.5 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Template CSV
            </a>
            <button type="button" onclick="openImportSiswaModal()" 
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import CSV Siswa
            </button>
            <span class="rounded-full bg-amber-100 text-amber-800 px-3.5 py-1 text-xs font-bold shadow-xs">
                {{ $siswas->count() }} Siswa Registered
            </span>
        </div>
    </div>

    <!-- TAMBAH SISWA FORM -->
    <div class="mb-6 bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-slate-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#4a101d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            Tambah Akun Siswa Baru
        </h2>
        <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @csrf
            <input type="hidden" name="from" value="superadmin">
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">NISN (10 Digit)</label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10" inputmode="numeric" pattern="[0-9]*" required 
                       placeholder="Masukkan NISN 10 digit"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required 
                       placeholder="Masukkan Nama Lengkap"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Kelas</label>
                <select name="kelas" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition bg-white">
                    <option value="" disabled {{ old('kelas') ? '' : 'selected' }}>-- Pilih Kelas --</option>
                    @foreach (['10 1', '10 2', '11 1', '11 2', '12 1', '12 2'] as $k)
                        <option value="{{ $k }}" {{ old('kelas') === $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" min="2020" required
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Jurusan</label>
                <select name="jurusan" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition bg-white">
                    <option value="" disabled {{ old('jurusan') ? '' : 'selected' }}>-- Pilih Jurusan --</option>
                    @foreach ([
                        'Teknik Alat Berat',
                        'Teknik Kendaraan Ringan',
                        'Teknik Sepeda Motor',
                        'Teknik Pemesinan',
                        'Teknik Instalasi Listrik',
                        'Teknik Pembangkit Listrik',
                        'Teknik Mekatronika',
                        'Teknik Audio Video',
                        'Teknik Komputer & Jaringan',
                        'Teknik Konstruksi & Perumahan',
                        'Desain Permodelan & Informasi Bangunan',
                        'Desain Komunikasi Visual',
                    ] as $j)
                        <option value="{{ $j }}" {{ old('jurusan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
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
                <button type="button" onclick="openImportSiswaModal()" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-700 transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Import CSV Siswa
                </button>
                <button type="submit" class="rounded-xl bg-amber-500 px-5 py-2.5 text-xs font-bold text-slate-950 shadow-md hover:bg-amber-400 transition cursor-pointer">
                    + Tambah Akun Siswa
                </button>
            </div>
        </form>
    </div>

    <!-- DAFTAR SISWA TABLE -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-slate-900">Daftar Akun Siswa Registered</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-700 uppercase tracking-wider font-bold">
                        <th class="px-4 py-3 rounded-l-xl">Foto</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Jurusan</th>
                        <th class="px-4 py-3 text-right rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($siswas as $siswa)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                @if ($siswa->foto_profile)
                                    <img src="{{ asset('storage/' . $siswa->foto_profile) }}" alt="{{ $siswa->nama_lengkap }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-xs shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-xs shrink-0 border border-amber-200/80 shadow-xs">
                                        {{ strtoupper(substr($siswa->nama_lengkap, 0, 2)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-normal text-slate-500 font-mono tracking-tight">{{ $siswa->nisn }}</td>
                            <td class="px-4 py-3 font-normal text-slate-700">{{ $siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-50 text-blue-700 border border-blue-200/60 font-medium px-2.5 py-0.5 rounded-full text-[10px]">Kelas {{ $siswa->kelas }}</span>
                            </td>
                            <td class="px-4 py-3 font-normal text-slate-600">{{ $siswa->tahun }}</td>
                            <td class="px-4 py-3 font-normal text-slate-600">{{ $siswa->jurusan }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button"
                                            onclick="openEditSiswaModal('{{ $siswa->nisn }}', '{{ addslashes($siswa->nama_lengkap) }}', '{{ $siswa->kelas }}', '{{ $siswa->tahun }}', '{{ addslashes($siswa->jurusan) }}')"
                                            class="text-amber-600 font-medium hover:underline cursor-pointer">Edit</button>
                                    <form action="{{ route('siswa.destroy', $siswa) }}" method="POST" onsubmit="return confirm('Hapus akun siswa ini?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="from" value="superadmin">
                                        <button type="submit" class="text-red-600 font-medium hover:underline cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-medium">Belum ada akun siswa registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
