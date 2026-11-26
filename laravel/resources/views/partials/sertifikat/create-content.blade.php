@php
    $btnPrimaryCls = $isSuperadmin
        ? 'bg-[#6D1837] hover:bg-[#521128]'
        : 'bg-[#7B1038] hover:bg-[#5E0C2B]';
    $btnGroupHoverCls = $isSuperadmin
        ? 'bg-[#6D1837] group-hover:bg-[#521128]'
        : 'bg-[#7B1038] group-hover:bg-[#5E0C2B]';
    $accentTextCls = $isSuperadmin ? 'text-[#6D1837]' : 'text-[#7B1038]';
    $accentLinkCls = $isSuperadmin ? 'text-[#6D1837] hover:text-[#521128]' : 'text-[#7B1038] hover:text-[#5E0C2B]';
    $badgeBgCls = $isSuperadmin ? 'bg-[#6D1837]' : 'bg-[#7B1038]';
    $focusFieldCls = $isSuperadmin
        ? 'focus:ring-[#6D1837]/20 focus:border-[#6D1837]'
        : 'focus:ring-[#7B1038]/20 focus:border-[#7B1038]';
    $fileBorderHoverCls = $isSuperadmin ? 'hover:border-[#6D1837]' : 'hover:border-[#7B1038]';
@endphp

<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mb-1">Terbitkan Sertifikat untuk Siswa</h1>
            <p class="text-xs sm:text-sm text-slate-500">Unggah berkas dokumen sertifikat resmi yang akan didistribusikan ke seluruh siswa terpilih.</p>
        </div>

        <a href="{{ route($routePrefix . '.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 p-4 text-xs sm:text-sm shadow-2xs">
            <p class="font-bold mb-1">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Terbitkan Sertifikat --}}
    <div class="bg-slate-50/70 rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs">
        <form action="{{ route($routePrefix . '.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Daftar Siswa Penerima Terpilih --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700">
                        Siswa Penerima
                    </label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $badgeBgCls }} text-white shadow-2xs">
                        {{ $siswaTerpilih->count() }} siswa terpilih
                    </span>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-100 shadow-2xs">
                    @foreach ($siswaTerpilih as $s)
                        @php
                            $posttestMap = $s->leaderboard
                                ->filter(fn ($lb) => $lb->quiz && $lb->quiz->id_materi)
                                ->groupBy(fn ($lb) => (string) $lb->quiz->id_materi)
                                ->map(fn ($grp) => (int) $grp->max('total_poin'));
                        @endphp
                        <div class="siswa-target-row flex items-center justify-between gap-3 px-3.5 py-2.5" data-posttests='@json($posttestMap)'>
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 {{ $accentTextCls }} flex items-center justify-center font-bold text-xs shrink-0 border border-slate-200">
                                    {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-slate-900 leading-tight truncate">{{ $s->nama_lengkap }}</p>
                                    <p class="text-[11px] text-slate-400">Kelas {{ str_replace(' ', '.', $s->kelas) }} | {{ $s->jurusan }}</p>
                                </div>
                            </div>
                            <span class="siswa-posttest-badge hidden inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border shrink-0"></span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-1.5">
                    <a href="{{ route($routePrefix . '.index') }}" class="inline-flex items-center gap-1 text-[11px] font-bold {{ $accentLinkCls }} transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Ubah daftar pilihan siswa
                    </a>
                </div>
            </div>

            {{-- Pilih Nama Materi (Integrasi id_materi) --}}
            <div>
                <label for="id_materi" class="block text-xs font-bold text-slate-700 mb-1">
                    Nama Materi
                    @if (! $isSuperadmin)
                        <span class="text-slate-400 font-normal">(Opsional &mdash; pilih jika sertifikat kelulusan materi)</span>
                    @endif
                </label>
                <select
                    name="id_materi"
                    id="id_materi"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 {{ $focusFieldCls }} transition"
                >
                    <option value="">-- Umum / Tidak Terkait Materi --</option>
                    @foreach ($daftarMateri as $m)
                        <option value="{{ $m->id_materi }}" @selected((string) old('id_materi', $selectedMateriId ?? '') === (string) $m->id_materi)>
                            {{ $m->judul_materi }}{{ $m->kelas ? ' — Kelas ' . $m->kelas . ($m->jurusan ? ' (' . $m->jurusan . ')' : '') : '' }}
                        </option>
                    @endforeach
                </select>
                @if (! $isSuperadmin)
                    <p class="text-[11px] text-slate-400 mt-1">Jika materi dipilih, kolom <code class="text-[10px] bg-slate-100 px-1 py-0.5 rounded">id_materi</code> dan kategori sertifikat kelulusan modul akan terhubung otomatis.</p>
                @endif
            </div>

            {{-- Judul Sertifikat --}}
            <div>
                <label for="judul_sertifikat" class="block text-xs font-bold text-slate-700 mb-1">
                    Judul Sertifikat <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="judul_sertifikat"
                    id="judul_sertifikat"
                    value="{{ old('judul_sertifikat') }}"
                    placeholder="Contoh: Sertifikat Kelulusan Modul Pemrograman Web"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 {{ $focusFieldCls }} transition"
                    required
                >
            </div>

            {{-- Grid Mini: Instansi Penerbit & Tanggal Input --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="penerbit" class="block text-xs font-bold text-slate-700 mb-1">
                        Instansi Penerbit
                    </label>
                    <input
                        type="text"
                        name="penerbit"
                        id="penerbit"
                        value="{{ old('penerbit', 'SMKN 2 Jember') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 {{ $focusFieldCls }} transition"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tanggal Input
                    </label>
                    <input
                        type="text"
                        value="{{ now()->translatedFormat('d F Y') }}"
                        readonly
                        disabled
                        class="w-full rounded-xl border border-slate-200 bg-slate-100/80 px-3.5 py-2 text-xs font-semibold text-slate-500 cursor-not-allowed select-none"
                        title="Tanggal diisi otomatis oleh sistem"
                    >
                </div>
            </div>

            {{-- Deskripsi / Catatan --}}
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-1">
                    Deskripsi / Catatan <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    rows="3"
                    placeholder="Catatan prestasi, kompetensi, atau rincian capaian pembelajaran siswa..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs focus:outline-none focus:ring-2 {{ $focusFieldCls }} transition"
                >{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Berkas File Sertifikat --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Berkas Sertifikat <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <label for="file_sertifikat" class="flex items-center gap-3 w-full p-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl cursor-pointer {{ $fileBorderHoverCls }} transition group shadow-2xs">
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 {{ $btnGroupHoverCls }} text-white text-xs font-bold rounded-lg transition duration-150 shadow-xs shrink-0 select-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Pilih File
                        </span>
                        <span id="file-chosen-text-upload" class="text-xs text-slate-500 truncate font-medium flex-1">
                            Belum ada berkas dipilih
                        </span>
                        <span class="text-[11px] font-semibold {{ $accentTextCls }} bg-slate-100 group-hover:bg-slate-200 px-2.5 py-1 rounded-md shrink-0 hidden sm:inline-block border border-slate-200 transition">
                            Telusuri
                        </span>
                    </label>
                    <input
                        type="file"
                        name="file_sertifikat"
                        id="file_sertifikat"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="sr-only"
                        onchange="const f = this.files[0]; const t = document.getElementById('file-chosen-text-upload'); if(f) { t.innerHTML = '<span class=\'text-emerald-700 font-bold\'>' + {{ $isSuperadmin ? "''" : "'✓ '" }} + f.name + '</span> <span class=\'text-slate-400 font-normal\'>(' + (f.size/1024/1024).toFixed(2) + ' MB)</span>'; } else { t.textContent = 'Belum ada berkas dipilih'; }"
                        required
                    >
                </div>
                <p class="text-slate-400 text-[10px] mt-1.5">Format: PDF, JPG, JPEG, PNG (Maks. 2 MB) &mdash; berkas akan didistribusikan langsung ke portofolio seluruh siswa terpilih.</p>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                <a
                    href="{{ route($routePrefix . '.index') }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-200/80 hover:bg-slate-300 transition"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 {{ $btnPrimaryCls }} text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition duration-150 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Terbitkan ke {{ $siswaTerpilih->count() }} Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectMateri = document.getElementById('id_materi');
    const rows = document.querySelectorAll('.siswa-target-row');
    if (!selectMateri || !rows.length) return;

    function refreshPosttestBadges() {
        const materiId = selectMateri.value;
        rows.forEach(function (row) {
            const badge = row.querySelector('.siswa-posttest-badge');
            if (!badge) return;

            if (!materiId) {
                badge.classList.add('hidden');
                return;
            }

            let map = {};
            try {
                map = JSON.parse(row.dataset.posttests || '{}');
            } catch (e) {
                map = {};
            }

            badge.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'bg-amber-50', 'text-amber-700', 'border-amber-200', 'bg-slate-100', 'text-slate-500', 'border-slate-200');

            if (Object.prototype.hasOwnProperty.call(map, materiId)) {
                const poin = Number(map[materiId]);
                badge.textContent = 'Post-Test: ' + poin;
                if (poin >= 75) {
                    badge.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-200');
                } else {
                    badge.classList.add('bg-amber-50', 'text-amber-700', 'border-amber-200');
                }
            } else {
                badge.textContent = 'Belum Post-Test';
                badge.classList.add('bg-slate-100', 'text-slate-500', 'border-slate-200');
            }
        });
    }

    selectMateri.addEventListener('change', refreshPosttestBadges);
    refreshPosttestBadges();
});
</script>
