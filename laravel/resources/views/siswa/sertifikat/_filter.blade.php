@php
    $fmt = fn ($d) => $d ? $d->copy()->locale('id')->translatedFormat('d M Y') : null;
    $tahunIni = now()->year;
    $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

{{-- Baris judul + tombol Filter --}}
<div class="flex items-center justify-between">
    <h3 class="text-xl font-semibold text-slate-900">Daftar Sertifikat</h3>

    <div class="relative" id="filterWrap">
        <button type="button" id="btnFilter" aria-expanded="false"
                class="inline-flex items-center px-5 py-2.5 text-base font-semibold rounded-xl border transition duration-150 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 {{ $filterAktif ? 'bg-[#7B1038] text-white border-[#7B1038] shadow-xs hover:bg-[#5E0C2B]' : 'bg-white text-slate-700 border-slate-300 hover:border-[#7B1038] hover:text-[#7B1038] shadow-2xs' }}">
            <span>Filter{{ $filterAktif ? ' (Aktif)' : '' }}</span>
        </button>

        {{-- Panel filter (tersembunyi sampai tombol diklik) --}}
        <form id="formFilter" method="GET" action="{{ route('siswa.sertifikat.index') }}"
            class="hidden absolute right-0 mt-2 z-50 w-[480px] max-w-[calc(100vw-2rem)] bg-white border border-slate-200 rounded-2xl shadow-lg p-6 space-y-5">

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <p class="text-xl font-semibold text-slate-900">Filter Tanggal Terbit</p>
                <button type="button" id="btnTutupFilter" class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition" aria-label="Tutup">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Rentang tanggal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="fltDari" class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" id="fltDari" name="dari" value="{{ $dari?->toDateString() }}"
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl bg-white text-slate-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                </div>
                <div>
                    <label for="fltSampai" class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" id="fltSampai" name="sampai" value="{{ $sampai?->toDateString() }}"
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-xl bg-white text-slate-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition">
                </div>
            </div>

            {{-- Pintasan --}}
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-sm font-medium text-gray-700 mr-1">Pintasan:</span>
                <button type="button" data-preset="bulan-ini"
                        class="px-4 py-2 text-sm font-semibold rounded-full border border-slate-200 bg-slate-50 text-slate-700 hover:bg-[#7B1038] hover:border-[#7B1038] hover:text-white transition duration-150 cursor-pointer shadow-2xs">Bulan ini</button>
                <button type="button" data-preset="3-bulan"
                        class="px-4 py-2 text-sm font-semibold rounded-full border border-slate-200 bg-slate-50 text-slate-700 hover:bg-[#7B1038] hover:border-[#7B1038] hover:text-white transition duration-150 cursor-pointer shadow-2xs">3 bulan terakhir</button>
                <button type="button" data-preset="tahun-ini"
                        class="px-4 py-2 text-sm font-semibold rounded-full border border-slate-200 bg-slate-50 text-slate-700 hover:bg-[#7B1038] hover:border-[#7B1038] hover:text-white transition duration-150 cursor-pointer shadow-2xs">Tahun ini</button>
                <button type="button" data-preset="1-tahun"
                        class="px-4 py-2 text-sm font-semibold rounded-full border border-slate-200 bg-slate-50 text-slate-700 hover:bg-[#7B1038] hover:border-[#7B1038] hover:text-white transition duration-150 cursor-pointer shadow-2xs">1 tahun terakhir</button>
            </div>

            {{-- Pilih bulan + tahun --}}
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-sm font-medium text-gray-700 mr-1">Atau pilih:</span>
                <select id="fltBulan" class="px-4 py-2 text-sm border border-slate-300 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition shadow-2xs">
                    <option value="">Semua bulan</option>
                    @foreach ($namaBulan as $i => $nama)
                        <option value="{{ $i + 1 }}">{{ $nama }}</option>
                    @endforeach
                </select>
                <select id="fltTahun" class="px-4 py-2 text-sm border border-slate-300 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#7B1038]/20 focus:border-[#7B1038] transition shadow-2xs">
                    <option value="">Pilih tahun</option>
                    @for ($t = $tahunIni; $t >= $tahunIni - 5; $t--)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endfor
                </select>
            </div>

            {{-- Tombol aksi --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('siswa.sertifikat.index') }}"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-base font-semibold rounded-xl transition">
                    Reset
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-base font-semibold rounded-xl shadow-xs transition duration-150 cursor-pointer">
                    Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Ringkasan filter aktif --}}
@if ($filterAktif)
    <div class="mt-3 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center gap-2 pl-3 pr-2 py-1 text-sm font-semibold text-white bg-[#7B1038] rounded-full shadow-2xs">
            <span>{{ $fmt($dari) ?? 'Awal' }} &ndash; {{ $fmt($sampai) ?? 'Sekarang' }}</span>
            <a href="{{ route('siswa.sertifikat.index') }}" title="Hapus filter"
            class="w-4 h-4 inline-flex items-center justify-center rounded-full hover:bg-[#5E0C2B] text-white">
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </span>
        <span class="text-sm font-medium text-slate-600">{{ $sertifikat->count() }} sertifikat ditemukan</span>
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
