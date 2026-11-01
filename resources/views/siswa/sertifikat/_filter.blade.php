@php
    $fmt = fn ($d) => $d ? $d->copy()->locale('id')->translatedFormat('d M Y') : null;
    $tahunIni = now()->year;
    $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

{{-- Baris judul + tombol Filter --}}
<div class="flex items-center justify-between">
    <h3 class="text-base font-semibold text-gray-800">Daftar sertifikat</h3>

    <div class="relative" id="filterWrap">
        <button type="button" id="btnFilter" aria-expanded="false"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-blue-700 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h8m4 0h4M4 12h4m4 0h8M4 18h10m4 0h2"/>
                <circle cx="14" cy="6" r="2"/>
                <circle cx="10" cy="12" r="2"/>
                <circle cx="16" cy="18" r="2"/>
            </svg>
            <span>Filter{{ $filterAktif ? ' (Aktif)' : '' }}</span>
        </button>

        {{-- Panel filter (tersembunyi sampai tombol diklik) --}}
        <form id="formFilter" method="GET" action="{{ route('siswa.sertifikat.index') }}"
              class="hidden absolute right-0 mt-2 z-30 w-[92vw] max-w-xl bg-white border border-gray-200 rounded-xl shadow-xl p-4">

            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-800">Filter tanggal terbit</p>
                <button type="button" id="btnTutupFilter" class="text-gray-400 hover:text-gray-700 text-xl leading-none" aria-label="Tutup">&times;</button>
            </div>

            {{-- Rentang tanggal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="fltDari" class="block text-xs font-medium text-gray-500 mb-1">Dari tanggal</label>
                    <input type="date" id="fltDari" name="dari" value="{{ $dari?->toDateString() }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="fltSampai" class="block text-xs font-medium text-gray-500 mb-1">Sampai tanggal</label>
                    <input type="date" id="fltSampai" name="sampai" value="{{ $sampai?->toDateString() }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            {{-- Pintasan --}}
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="text-xs text-gray-500 mr-1">Pintasan:</span>
                <button type="button" data-preset="bulan-ini"
                        class="px-3 py-1 text-xs font-medium rounded-full border border-gray-300 text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition">Bulan ini</button>
                <button type="button" data-preset="3-bulan"
                        class="px-3 py-1 text-xs font-medium rounded-full border border-gray-300 text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition">3 bulan terakhir</button>
                <button type="button" data-preset="tahun-ini"
                        class="px-3 py-1 text-xs font-medium rounded-full border border-gray-300 text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition">Tahun ini</button>
                <button type="button" data-preset="1-tahun"
                        class="px-3 py-1 text-xs font-medium rounded-full border border-gray-300 text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition">1 tahun terakhir</button>
            </div>

            {{-- Pilih bulan + tahun --}}
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs text-gray-500 mr-1">Atau pilih:</span>
                <select id="fltBulan" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua bulan</option>
                    @foreach ($namaBulan as $i => $nama)
                        <option value="{{ $i + 1 }}">{{ $nama }}</option>
                    @endforeach
                </select>
                <select id="fltTahun" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih tahun</option>
                    @for ($t = $tahunIni; $t >= $tahunIni - 6; $t--)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endfor
                </select>
            </div>

            {{-- Tombol aksi --}}
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                <a href="{{ route('siswa.sertifikat.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Reset
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Ringkasan filter aktif --}}
@if ($filterAktif)
    <div class="mt-3 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center gap-2 pl-3 pr-2 py-1 text-xs font-medium text-blue-800 bg-blue-50 border border-blue-200 rounded-full">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a1 1 0 001-1V7a1 1 0 00-1-1H5a1 1 0 00-1 1v13a1 1 0 001 1z"/>
            </svg>
            {{ $fmt($dari) ?? 'Awal' }} &ndash; {{ $fmt($sampai) ?? 'Sekarang' }}
            <a href="{{ route('siswa.sertifikat.index') }}" title="Hapus filter"
               class="w-5 h-5 inline-flex items-center justify-center rounded-full hover:bg-blue-100">&times;</a>
        </span>
        <span class="text-xs text-gray-500">{{ $sertifikat->count() }} sertifikat ditemukan</span>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {
    const wrap   = document.getElementById('filterWrap');
    const btn    = document.getElementById('btnFilter');
    const panel  = document.getElementById('formFilter');
    const form   = panel;
    const dari   = document.getElementById('fltDari');
    const sampai = document.getElementById('fltSampai');
    const bulan  = document.getElementById('fltBulan');
    const tahun  = document.getElementById('fltTahun');

    // Buka / tutup panel
    const buka  = () => { panel.classList.remove('hidden'); btn.setAttribute('aria-expanded', 'true'); };
    const tutup = () => { panel.classList.add('hidden'); btn.setAttribute('aria-expanded', 'false'); };

    btn.addEventListener('click', () => panel.classList.contains('hidden') ? buka() : tutup());
    document.getElementById('btnTutupFilter').addEventListener('click', tutup);
    document.addEventListener('click', e => { if (!wrap.contains(e.target)) tutup(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutup(); });

    // Helper tanggal
    const pad = n => String(n).padStart(2, '0');
    const iso = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

        const terapkan = (start, end) => {
        dari.value = iso(start);
        sampai.value = iso(end);
    };

    // Pintasan cepat
    document.querySelectorAll('[data-preset]').forEach(b => {
        b.addEventListener('click', () => {
            const now = new Date();
            const y = now.getFullYear();
            const m = now.getMonth();

            switch (b.dataset.preset) {
                case 'bulan-ini':
                    terapkan(new Date(y, m, 1), new Date(y, m + 1, 0));
                    break;
                case '3-bulan':
                    terapkan(new Date(y, m - 2, 1), now);
                    break;
                case 'tahun-ini':
                    terapkan(new Date(y, 0, 1), new Date(y, 11, 31));
                    break;
                case '1-tahun':
                    terapkan(new Date(y - 1, m, now.getDate()), now);
                    break;
            }
        });
    });

        // Pilih bulan + tahun (tanpa submit, tunggu klik Terapkan)
    const dariBulanTahun = () => {
        if (!tahun.value) return;
        const y = parseInt(tahun.value, 10);

        if (bulan.value) {
            const m = parseInt(bulan.value, 10) - 1;
            dari.value = iso(new Date(y, m, 1));
            sampai.value = iso(new Date(y, m + 1, 0));
        } else {
            dari.value = iso(new Date(y, 0, 1));
            sampai.value = iso(new Date(y, 11, 31));
        }
    };

    tahun.addEventListener('change', dariBulanTahun);
    bulan.addEventListener('change', () => {
        if (!tahun.value) tahun.value = String(new Date().getFullYear());
        dariBulanTahun();
    });
});
</script>