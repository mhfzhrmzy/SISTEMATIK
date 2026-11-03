<!-- EDIT GURU MODAL -->
<div id="edit-guru-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-base">Edit Akun Guru</h3>
            <button type="button" onclick="closeEditGuruModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2 cursor-pointer">&times;</button>
        </div>
        <form id="edit-guru-form" action="" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            @method('PUT')
            <input type="hidden" name="from" value="superadmin">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                <input type="text" id="edit-guru-nip" name="nip" maxlength="18" inputmode="numeric" pattern="[0-9]*" required readonly class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs text-slate-500 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="edit-guru-nama" name="nama_lengkap" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru <span class="font-normal text-slate-400">(Opsional)</span></label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Foto Profil <span class="font-normal text-slate-400">(Opsional)</span></label>
                <input type="file" name="foto_profile" accept="image/*" class="w-full text-xs text-slate-500">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeEditGuruModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-[#4a101d] hover:bg-[#380b15] rounded-xl transition cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT SISWA MODAL -->
<div id="edit-siswa-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-base">Edit Akun Siswa</h3>
            <button type="button" onclick="closeEditSiswaModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2 cursor-pointer">&times;</button>
        </div>
        <form id="edit-siswa-form" action="" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            @method('PUT')
            <input type="hidden" name="from" value="superadmin">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">NISN (Nomor Induk Siswa Nasional)</label>
                <input type="text" id="edit-siswa-nisn" name="nisn" maxlength="10" inputmode="numeric" pattern="[0-9]*" required readonly class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2 text-xs text-slate-500 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="edit-siswa-nama" name="nama_lengkap" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kelas</label>
                <select id="edit-siswa-kelas" name="kelas" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition bg-white">
                    <option value="10 1">Kelas 10 1</option>
                    <option value="10 2">Kelas 10 2</option>
                    <option value="11 1">Kelas 11 1</option>
                    <option value="11 2">Kelas 11 2</option>
                    <option value="12 1">Kelas 12 1</option>
                    <option value="12 2">Kelas 12 2</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tahun</label>
                <input type="number" id="edit-siswa-tahun" name="tahun" min="2020" required
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Jurusan</label>
                <select id="edit-siswa-jurusan" name="jurusan" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition bg-white">
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
                        <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru <span class="font-normal text-slate-400">(Opsional)</span></label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#4a101d]/20 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Foto Profil <span class="font-normal text-slate-400">(Opsional)</span></label>
                <input type="file" name="foto_profile" accept="image/*" class="w-full text-xs text-slate-500">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeEditSiswaModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-[#4a101d] hover:bg-[#380b15] rounded-xl transition cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- IMPORT SISWA CSV MODAL -->
<div id="import-siswa-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import Data Siswa (CSV)
            </h3>
            <button type="button" onclick="closeImportSiswaModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2 cursor-pointer">&times;</button>
        </div>
        <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="from" value="superadmin">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih File CSV (.csv)</label>
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-[11px] text-slate-400 mt-1.5">Format kolom: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-mono">nisn, nama_lengkap, kelas, tahun, jurusan, password</code></p>
            </div>

            <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-100 text-xs text-emerald-900 flex items-center justify-between">
                <span>Belum punya contoh format?</span>
                <a href="{{ route('siswa.template') }}" class="font-bold text-emerald-700 hover:underline flex items-center gap-1">
                    Download Template CSV
                </a>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeImportSiswaModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

<!-- IMPORT GURU CSV MODAL -->
<div id="import-guru-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import Data Guru (CSV)
            </h3>
            <button type="button" onclick="closeImportGuruModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2 cursor-pointer">&times;</button>
        </div>
        <form action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="from" value="superadmin">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih File CSV (.csv)</label>
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-[11px] text-slate-400 mt-1.5">Format kolom: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-mono">nip, nama_lengkap, password</code></p>
            </div>

            <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-100 text-xs text-purple-900 flex items-center justify-between">
                <span>Belum punya contoh format?</span>
                <a href="{{ route('admin.template') }}" class="font-bold text-purple-700 hover:underline flex items-center gap-1">
                    Download Template CSV
                </a>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeImportGuruModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-purple-700 hover:bg-purple-800 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openImportGuruModal() {
        document.getElementById('import-guru-modal').classList.remove('hidden');
    }
    function closeImportGuruModal() {
        document.getElementById('import-guru-modal').classList.add('hidden');
    }

    function openImportSiswaModal() {
        document.getElementById('import-siswa-modal').classList.remove('hidden');
    }
    function closeImportSiswaModal() {
        document.getElementById('import-siswa-modal').classList.add('hidden');
    }

    function openEditGuruModal(nip, nama) {
        document.getElementById('edit-guru-form').action = "/admin/" + encodeURIComponent(nip);
        document.getElementById('edit-guru-nip').value = nip;
        document.getElementById('edit-guru-nama').value = nama;
        document.getElementById('edit-guru-modal').classList.remove('hidden');
    }
    function closeEditGuruModal() {
        document.getElementById('edit-guru-modal').classList.add('hidden');
    }

    function openEditSiswaModal(nisn, nama, kelas, tahun, jurusan) {
        document.getElementById('edit-siswa-form').action = "/siswa/" + encodeURIComponent(nisn);
        document.getElementById('edit-siswa-nisn').value = nisn;
        document.getElementById('edit-siswa-nama').value = nama;
        document.getElementById('edit-siswa-kelas').value = kelas;
        document.getElementById('edit-siswa-tahun').value = tahun;
        document.getElementById('edit-siswa-jurusan').value = jurusan;
        document.getElementById('edit-siswa-modal').classList.remove('hidden');
    }
    function closeEditSiswaModal() {
        document.getElementById('edit-siswa-modal').classList.add('hidden');
    }

    document.getElementById('superadmin-search-input')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (query === '' || text.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
