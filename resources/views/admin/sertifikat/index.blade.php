@extends('layouts.app')

@section('title', 'Penerbitan Sertifikat')

@section('content')
<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Penerbitan Sertifikat Siswa</h2>
        <p class="text-sm text-gray-500 mt-1">Filter dan pilih siswa penerima, lalu klik <strong>Tambahkan Siswa</strong> untuk melanjutkan ke form upload.</p>
    </div>

    @if (session('error'))
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form Pilih Siswa Target --}}
    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">

        @if ($errors->has('student_ids'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ $errors->first('student_ids') }}
            </div>
        @endif

        <div class="space-y-4">

            {{-- Filter Kelas & Jurusan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="filterKelas" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Filter Kelas</label>
                    <select id="filterKelas" class="w-full px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">— Semua Kelas —</option>
                        @foreach(['10 1', '10 2'] as $k)
                            <option value="{{ $k }}">Kelas {{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filterJurusan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Filter Jurusan</label>
                    <select id="filterJurusan" class="w-full px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">— Semua Jurusan —</option>
                        @foreach([
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
            </div>

            {{-- Header daftar siswa --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Daftar Siswa
                        <span id="jumlahTampil" class="ml-1.5 text-xs font-normal text-gray-400"></span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnCentangSemua"
                            class="px-3 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-300 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            ☑ Centang Semua
                        </button>
                        <button type="button" id="btnHapusCentang"
                            class="px-3 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-300 transition disabled:opacity-40 disabled:cursor-not-allowed"
                            disabled>
                            ☐ Hapus Centang
                        </button>
                    </div>
                </div>

                {{-- Pencarian --}}
                <div class="relative mb-2">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="searchSiswa" placeholder="Cari nama siswa..."
                        class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                        autocomplete="off">
                </div>

                {{-- Checkbox list --}}
                <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                    <ul id="siswaCheckboxList" class="divide-y divide-gray-100 max-h-72 overflow-y-auto"></ul>
                    <div id="siswaKosong" class="hidden px-4 py-8 text-center text-sm text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tidak ada siswa yang sesuai filter.
                    </div>
                    <div id="siswaLoading" class="px-4 py-8 text-center text-sm text-gray-400">
                        Memuat daftar siswa...
                    </div>
                </div>
            </div>

            {{-- Badge ringkasan --}}
            <div id="ringkasanTerpilih" class="hidden">
                <div class="mb-1.5">
                    <span class="text-sm font-semibold text-gray-700">
                        Siswa yang akan diberi sertifikat
                        <span id="badgeJumlah" class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">0</span>
                    </span>
                </div>
                <div id="badgeContainer" class="flex flex-wrap gap-1.5"></div>
            </div>

            {{-- Form submit --}}
            <form action="{{ route('sertifikat.create') }}" method="POST" id="formPilihSiswa">
                @csrf
                <div id="hiddenInputsContainer"></div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                    <p class="text-xs text-gray-400">Centang siswa yang diinginkan, lalu klik tombol di kanan.</p>
                    <button type="submit" id="btnTambahkanSiswa"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambahkan Siswa &amp; Lanjut Upload
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Riwayat Sertifikat --}}
    <div>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Riwayat Sertifikat yang Telah Diterbitkan Guru</h3>
        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase bg-gray-50 border-b border-gray-200 text-gray-600">
                    <tr>
                        <th class="px-5 py-3">Nama Siswa</th>
                        <th class="px-5 py-3">Kelas &amp; Jurusan</th>
                        <th class="px-5 py-3">Judul Sertifikat</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sertifikat as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs">
                                @if ($item->siswa)
                                    Kelas {{ $item->siswa->kelas }} — {{ $item->siswa->jurusan }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-3.5">{{ $item->judul_sertifikat }}</td>
                            <td class="px-5 py-3.5 text-center space-x-3">
                                <a href="{{ asset('storage/' . $item->file_sertifikat) }}" target="_blank"
                                    class="text-blue-600 hover:underline text-xs font-semibold">Lihat Berkas</a>
                                <form action="{{ route('sertifikat.destroy', $item->id_sertifikat) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Hapus sertifikat ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-500">
                                Belum ada sertifikat yang diterbitkan oleh guru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    #siswaCheckboxList { scrollbar-width: thin; scrollbar-color: #d1d5db #f9fafb; }
    #siswaCheckboxList::-webkit-scrollbar { width: 4px; }
    #siswaCheckboxList::-webkit-scrollbar-track { background: #f9fafb; }
    #siswaCheckboxList::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
    .siswa-row { transition: background-color .1s; cursor: pointer; user-select: none; }
    .siswa-row:hover { background-color: #f0f9ff; }
    .siswa-row.is-checked { background-color: #eff6ff; }
</style>

<script>
(function () {
    'use strict';

    /* ══ STATE ══ */
    let allSiswa      = [];
    let filteredSiswa = [];
    let checked       = new Set(); // NISN yang dicentang

    /* ══ DOM ══ */
    const elFilterKelas  = document.getElementById('filterKelas');
    const elFilterJurusan= document.getElementById('filterJurusan');
    const elSearch       = document.getElementById('searchSiswa');
    const elList         = document.getElementById('siswaCheckboxList');
    const elEmpty        = document.getElementById('siswaKosong');
    const elLoading      = document.getElementById('siswaLoading');
    const elJumlah       = document.getElementById('jumlahTampil');
    const elBtnSemua     = document.getElementById('btnCentangSemua');
    const elBtnHapus     = document.getElementById('btnHapusCentang');
    const elRingkasan    = document.getElementById('ringkasanTerpilih');
    const elBadges       = document.getElementById('badgeContainer');
    const elBadgeJumlah  = document.getElementById('badgeJumlah');
    const elHidden       = document.getElementById('hiddenInputsContainer');
    const elBtnSubmit    = document.getElementById('btnTambahkanSiswa');
    const elForm         = document.getElementById('formPilihSiswa');

    /* ══ FETCH ══ */
    async function fetchSiswa() {
        elLoading.classList.remove('hidden');
        try {
            const r = await fetch('{{ route('sertifikat.siswa-json') }}');
            allSiswa = await r.json();
        } catch (e) {
            console.error(e);
            allSiswa = [];
        }
        elLoading.classList.add('hidden');
        applyFilter();
    }

    /* ══ FILTER ══ */
    function applyFilter() {
        const k = elFilterKelas.value;
        const j = elFilterJurusan.value;
        const q = elSearch.value.trim().toLowerCase();

        filteredSiswa = allSiswa.filter(s =>
            (!k || s.kelas   === k) &&
            (!j || s.jurusan === j) &&
            (!q || s.nama_lengkap.toLowerCase().includes(q))
        );

        renderList();
        syncButtons();
    }

    /* ══ RENDER LIST ══
     *  Setiap item adalah <li> dengan satu event listener "click" saja.
     *  TIDAK ada <input type="checkbox"> native sehingga TIDAK ada
     *  double-fire dari label+change event. Visual checkbox dibuat dengan <span>.
     */
    function renderList() {
        elList.innerHTML = '';

        if (!filteredSiswa.length) {
            elEmpty.classList.remove('hidden');
            elList.classList.add('hidden');
            elJumlah.textContent = '';
            return;
        }
        elEmpty.classList.add('hidden');
        elList.classList.remove('hidden');
        elJumlah.textContent = `(${filteredSiswa.length} siswa tampil)`;

        filteredSiswa.forEach(s => {
            const on = checked.has(s.nisn);
            const li = document.createElement('li');
            li.className    = 'siswa-row' + (on ? ' is-checked' : '');
            li.dataset.nisn = s.nisn;
            li.innerHTML    = itemHTML(s, on);

            /* ── SATU listener, langsung di li ── */
            li.addEventListener('click', () => toggle(s.nisn));

            elList.appendChild(li);
        });
    }

    function itemHTML(s, on) {
        const boxCls = on
            ? 'border-blue-600 bg-blue-600'
            : 'border-gray-300 bg-white';
        const tick = on
            ? '<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
            : '';
        return `
            <div class="flex items-center gap-3 px-4 py-3">
                <span class="chk flex-shrink-0 flex w-4 h-4 rounded border-2 items-center justify-center transition-all ${boxCls}">${tick}</span>
                <span class="leading-tight">
                    <span class="block text-sm font-medium text-gray-800">${esc(s.nama_lengkap)}</span>
                    <span class="block text-xs text-gray-400">Kelas ${esc(s.kelas)} &mdash; ${esc(s.jurusan)}</span>
                </span>
            </div>`;
    }

    /* ══ TOGGLE — satu-satunya tempat state berubah ══ */
    function toggle(nisn) {
        checked.has(nisn) ? checked.delete(nisn) : checked.add(nisn);

        /* Update visual item yang diklik tanpa re-render seluruh list */
        const li = elList.querySelector(`[data-nisn="${nisn}"]`);
        if (li) {
            const on  = checked.has(nisn);
            const box = li.querySelector('.chk');
            li.classList.toggle('is-checked', on);
            if (box) {
                box.className = box.className
                    .replace(on ? 'border-gray-300 bg-white' : 'border-blue-600 bg-blue-600',
                             on ? 'border-blue-600 bg-blue-600' : 'border-gray-300 bg-white');
                box.innerHTML = on
                    ? '<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>'
                    : '';
            }
        }
        syncAll();
    }

    function syncAll() { syncButtons(); renderBadges(); updateHidden(); }

    /* ══ CENTANG SEMUA / HAPUS ══ */
    elBtnSemua.addEventListener('click', () => {
        filteredSiswa.forEach(s => checked.add(s.nisn));
        renderList(); syncAll();
    });
    elBtnHapus.addEventListener('click', () => {
        filteredSiswa.forEach(s => checked.delete(s.nisn));
        renderList(); syncAll();
    });

    /* ══ BADGE ══ */
    function renderBadges() {
        elBadges.innerHTML = '';
        if (!checked.size) { elRingkasan.classList.add('hidden'); return; }

        elRingkasan.classList.remove('hidden');
        elBadgeJumlah.textContent = checked.size;

        checked.forEach(nisn => {
            const s = allSiswa.find(x => x.nisn === nisn);
            if (!s) return;
            const span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 pl-2.5 pr-1 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200';
            span.innerHTML = `${esc(s.nama_lengkap)}<span class="text-blue-400">· Kls${esc(s.kelas)}</span>
                <button type="button" class="ml-0.5 w-3.5 h-3.5 rounded-full bg-blue-200 hover:bg-red-400 flex items-center justify-center transition" title="Hapus">
                    <svg class="w-2 h-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            span.querySelector('button').addEventListener('click', () => toggle(nisn));
            elBadges.appendChild(span);
        });
    }

    /* ══ HIDDEN INPUTS ══ */
    function updateHidden() {
        elHidden.innerHTML = '';
        checked.forEach(nisn => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'student_ids[]'; inp.value = nisn;
            elHidden.appendChild(inp);
        });
    }

    /* ══ BUTTON STATES ══ */
    function syncButtons() {
        const anyInFilter = filteredSiswa.some(s => checked.has(s.nisn));
        const allInFilter = filteredSiswa.length > 0 && filteredSiswa.every(s => checked.has(s.nisn));
        elBtnSemua.disabled  = !filteredSiswa.length || allInFilter;
        elBtnHapus.disabled  = !anyInFilter;
        elBtnSubmit.disabled = !checked.size;
    }

    /* ══ FORM GUARD ══ */
    elForm.addEventListener('submit', e => {
        if (!checked.size) { e.preventDefault(); alert('Centang minimal satu siswa terlebih dahulu.'); }
    });

    /* ══ EVENTS ══ */
    function onDropdownFilterChange() {
        checked.clear();
        applyFilter();
        syncAll();
    }

    elFilterKelas.addEventListener('change', onDropdownFilterChange);
    elFilterJurusan.addEventListener('change', onDropdownFilterChange);
    elSearch.addEventListener('input', applyFilter);

    /* ══ UTIL ══ */
    function esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ══ INIT ══ */
    fetchSiswa();
})();
</script>
@endsection