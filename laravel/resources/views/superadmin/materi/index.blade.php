@extends('layouts.app')

@section('title', 'Kelola Bahan Ajar — Superadmin Portal')

@section('content')
<div class="flex min-h-screen bg-slate-100 font-sans text-slate-800">
    <!-- LEFT SIDEBAR -->
    @include('superadmin.partials.sidebar', ['menu' => 'materi'])

    <!-- MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        @include('superadmin.partials.header')

        <!-- PAGE CONTENT -->
        <main class="p-6 lg:p-8 space-y-6">
            <!-- Header Row -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Kelola Bahan Ajar</h1>
                    <p class="text-xs text-slate-500 mt-1">Super Admin dapat menambahkan atau mengelola bahan ajar untuk membantu guru.</p>
                </div>
            </div>

            <!-- Flash Session Alerts -->
            @if(session('success'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 text-sm font-semibold space-y-1 shadow-sm">
                    <p class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Gagal menyimpan materi:
                    </p>
                    <ul class="list-disc list-inside text-xs space-y-0.5 pl-6 font-normal">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Tambah Bahan Ajar -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span>Tambah Bahan Ajar Baru</span>
                    </h2>

                    <form action="{{ route('superadmin.materi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Pilih Guru Pengampu <span class="text-red-500">*</span></label>
                            <select name="nip" id="nip" class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 bg-white {{ $errors->has('nip') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}" required>
                                <option value="">-- Pilih Guru Pengampu --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->nip }}" {{ old('nip') == $admin->nip ? 'selected' : '' }}>
                                        {{ $admin->nama_lengkap }} ({{ $admin->nip }})
                                    </option>
                                @endforeach
                            </select>
                            @error('nip')
                                <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Tahun Ajaran <span class="text-red-500">*</span></label>
                            <input type="number"
                                   name="tahun"
                                   id="tahun"
                                   value="{{ old('tahun', date('Y')) }}"
                                   min="2020"
                                   class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('tahun') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}"
                                   required>
                            @error('tahun')
                                <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Kelas <span class="text-red-500">*</span></label>
                            <select name="kelas" id="kelas" class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('kelas') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach(['X-1', 'X-2'] as $k)
                                    <option value="{{ $k }}" {{ old('kelas') == $k ? 'selected' : '' }}>
                                        Kelas {{ $k }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas')
                                <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Jurusan <span class="text-red-500">*</span></label>
                            <select name="jurusan" id="jurusan" class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('jurusan') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}" required>
                                <option value="">-- Pilih Jurusan --</option>
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
                                    <option value="{{ $j }}" {{ old('jurusan') == $j ? 'selected' : '' }}>
                                        {{ $j }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jurusan')
                                <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Judul Materi <span class="text-red-500">*</span></label>
                            <input type="text"
                                   name="judul_materi"
                                   id="judul_materi"
                                   value="{{ old('judul_materi') }}"
                                   pattern="[a-zA-Z0-9 ]+"
                                   oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')"
                                   placeholder="Contoh: Pemrograman Web Dasar 1"
                                   class="w-full border rounded-xl px-3 py-2 text-sm text-slate-700 {{ $errors->has('judul_materi') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}"
                                   required>
                            @error('judul_materi')
                                <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                            @else
                                <p class="text-slate-400 text-[11px] mt-1">Hanya huruf dan angka yang diperbolehkan.</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">Deskripsi Materi</label>
                            <textarea name="isi_materi" rows="3" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#6D1837]">{{ old('isi_materi') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-bold mb-1.5">
                                Upload File Modul <span class="text-red-500">*</span>
                                <span class="text-slate-400 font-normal text-[11px] block">(PDF, PPT, PPTX, DOC, DOCX — maks. 25 MB)</span>
                            </label>
                            <input type="file" name="upload_file" accept=".pdf,.ppt,.pptx,.doc,.docx"
                                   class="w-full text-xs text-slate-500 border rounded-xl px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#6D1837] file:text-white hover:file:bg-[#521128] {{ $errors->has('upload_file') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}" required>
                            @error('upload_file')
                                <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-[#6D1837] hover:bg-[#521128] text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-sm">
                            Simpan Bahan Ajar
                        </button>
                    </form>
                </div>

                <!-- Tabel Daftar Bahan Ajar -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Daftar Bahan Ajar</h2>
                            <p class="text-xs text-slate-500">Total {{ $materis->count() }} Bahan Ajar Terdaftar</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-700 uppercase tracking-wider font-bold">
                                    <th class="px-3 py-2.5 rounded-l-lg">Judul</th>
                                    <th class="px-3 py-2.5">Diampu Oleh</th>
                                    <th class="px-3 py-2.5">Kelas</th>
                                    <th class="px-3 py-2.5">Tahun</th>
                                    <th class="px-3 py-2.5">Jurusan</th>
                                    <th class="px-3 py-2.5">File</th>
                                    <th class="px-3 py-2.5 text-right rounded-r-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($materis as $item)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-3 py-3 font-semibold text-slate-800">
                                            {{ $item->judul_materi }}
                                            @if(!empty($item->isi_materi))
                                                <p class="text-[11px] text-slate-500 font-normal line-clamp-1 mt-0.5">{{ $item->isi_materi }}</p>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-slate-700 font-medium">
                                            {{ $item->guru?->nama_lengkap ?? '-' }}
                                        </td>
                                        <td class="px-3 py-3">
                                            @if($item->kelas)
                                                <span class="inline-block bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-md">
                                                    Kelas {{ $item->kelas }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">{{ $item->tahun ?? '-' }}</td>
                                        <td class="px-3 py-3 text-slate-600">{{ $item->jurusan ?? '-' }}</td>
                                        <td class="px-3 py-3">
                                            @if($item->upload_file)
                                                @php
                                                    $saFileUrl = route('superadmin.materi.show', $item);
                                                    $saFitUrl = $saFileUrl . '#page=1&view=Fit&navpanes=0';
                                                    $isSaPdf = str_ends_with(strtolower($item->upload_file ?? ''), '.pdf');
                                                @endphp
                                                @if($isSaPdf)
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <a href="{{ $saFitUrl }}"
                                                           target="_blank"
                                                           class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#6D1837] hover:bg-[#521128] text-white font-bold text-[11px] transition"
                                                           title="Pratinjau Pas 1 Halaman">
                                                            Pratinjau
                                                        </a>
                                                        <a href="{{ $saFitUrl }}"
                                                           target="_blank"
                                                           class="p-1 rounded-lg text-slate-500 hover:text-[#7B1038] hover:bg-slate-100 transition duration-150"
                                                           title="Buka di tab baru (Pas 1 Halaman)">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ $saFileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-[#6D1837] hover:underline font-bold">
                                                        📄 Unduh
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-slate-400 italic">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <div class="inline-flex items-center justify-end gap-2">
                                                <a href="{{ route('superadmin.materi.edit', $item) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-xs hover:underline">
                                                    Edit
                                                </a>
                                                <form action="{{ route('superadmin.materi.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-xs hover:underline cursor-pointer">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-6 text-slate-400 font-medium">Belum ada bahan ajar yang terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Modal Pratinjau PDF Materi Superadmin --}}
<div id="materi-preview-modal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5">
    <div id="materi-modal-card" class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-6xl h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        {{-- Header Bar --}}
        <div class="bg-slate-800 text-slate-200 px-4 py-3 flex items-center justify-between flex-wrap gap-2 text-xs border-b border-slate-700 select-none">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#6D1837] text-white text-[10px] font-bold uppercase tracking-wide">
                    PRATINJAU SUPERADMIN
                </span>
                <span id="modal-materi-title" class="font-bold text-slate-100 truncate max-w-[220px] sm:max-w-md">
                    Pratinjau Modul
                </span>
            </div>

        {{-- Mode & Action Controls --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button"
                    id="modal-btn-landscape"
                    onclick="switchModalPdfMode('landscape')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#6D1837] text-white transition shadow-2xs cursor-pointer"
                    title="Format Landscape / Slide Presentasi (16:9 Pas 1 Halaman)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2" stroke-width="2"/></svg>
                <span>Landscape</span>
            </button>
            <button type="button"
                    id="modal-btn-portrait"
                    onclick="switchModalPdfMode('portrait')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-300 transition cursor-pointer"
                    title="Format Portrait / Dokumen A4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="6" y="3" width="12" height="18" rx="2" stroke-width="2"/></svg>
                <span>Portrait</span>
            </button>
            <button type="button"
                    id="modal-btn-fit"
                    onclick="switchModalPdfMode('fit')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-300 transition cursor-pointer"
                    title="Pas Layar Penuh">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span>Pas Layar</span>
            </button>
            <span class="w-px h-4 bg-slate-700 mx-1"></span>
            <button type="button"
                    onclick="toggleModalFullscreen()"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition cursor-pointer"
                    title="Layar Penuh">
                <span id="modal-text-fs">⛶ Layar Penuh</span>
            </button>
            <a id="modal-tab-link" href="#" target="_blank"
               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition"
               title="Buka di Tab Baru">
                <span>↗ Tab Baru</span>
            </a>
            <button type="button"
                    onclick="closeMateriPreview()"
                    class="p-1 rounded-lg bg-slate-700 hover:bg-rose-600 text-slate-300 hover:text-white transition ml-1 cursor-pointer"
                    title="Tutup (Esc)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        </div>

        {{-- Iframe --}}
        <div class="relative w-full flex-1 bg-slate-900">
            <iframe id="modal-pdf-iframe"
                    src="about:blank"
                    class="w-full h-full border-0 block"
                    allow="fullscreen"
                    title="Pratinjau Modul">
            </iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentModalBaseUrl = '';
    let currentModalMode = 'landscape';

    function applyModalCardDimensions() {
        const card = document.getElementById('materi-modal-card');
        if (!card) return;
        if (document.fullscreenElement) {
            card.style.maxWidth = '100vw';
            card.style.height = '100vh';
            return;
        }

        const availHeight = Math.max(480, window.innerHeight - 70);

        if (currentModalMode === 'landscape') {
            card.style.maxWidth = '1020px';
            const w = card.clientWidth || 980;
            const targetH = Math.round((w * 9 / 16) + 48);
            card.style.height = Math.max(480, Math.min(availHeight, targetH)) + 'px';
        } else if (currentModalMode === 'portrait') {
            card.style.maxWidth = '840px';
            card.style.height = Math.min(availHeight, 880) + 'px';
        } else {
            card.style.maxWidth = '1200px';
            card.style.height = availHeight + 'px';
        }
    }

    function openMateriPreview(fitUrl, rawUrl, title) {
        currentModalBaseUrl = rawUrl;
        document.getElementById('modal-materi-title').innerText = title;
        document.getElementById('modal-tab-link').href = fitUrl;
        document.getElementById('materi-preview-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        switchModalPdfMode('landscape');
    }

    function closeMateriPreview() {
        const modal = document.getElementById('materi-preview-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.getElementById('modal-pdf-iframe').src = 'about:blank';
        document.body.classList.remove('overflow-hidden');
        if (document.fullscreenElement) {
            document.exitFullscreen().catch(() => {});
        }
    }

    function switchModalPdfMode(mode) {
        currentModalMode = mode;
        const iframe = document.getElementById('modal-pdf-iframe');
        const btnLandscape = document.getElementById('modal-btn-landscape');
        const btnPortrait = document.getElementById('modal-btn-portrait');
        const btnFit = document.getElementById('modal-btn-fit');
        if (!iframe || !currentModalBaseUrl) return;

        [btnLandscape, btnPortrait, btnFit].forEach(btn => {
            if (!btn) return;
            btn.classList.remove('bg-[#6D1837]', 'text-white', 'shadow-2xs');
            btn.classList.add('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
        });

        const activeBtn = mode === 'landscape' ? btnLandscape : (mode === 'portrait' ? btnPortrait : btnFit);
        if (activeBtn) {
            activeBtn.classList.remove('bg-slate-700', 'text-slate-300', 'hover:bg-slate-600');
            activeBtn.classList.add('bg-[#6D1837]', 'text-white', 'shadow-2xs');
        }

        applyModalCardDimensions();
        iframe.src = currentModalBaseUrl + '#page=1&view=Fit&navpanes=0';
    }

    function toggleModalFullscreen() {
        const card = document.getElementById('materi-modal-card');
        if (!card) return;
        if (!document.fullscreenElement) {
            if (card.requestFullscreen) card.requestFullscreen();
            else if (card.webkitRequestFullscreen) card.webkitRequestFullscreen();
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('materi-preview-modal');
            if (modal && !modal.classList.contains('hidden') && !document.fullscreenElement) {
                closeMateriPreview();
            }
        }
    });

    document.addEventListener('fullscreenchange', () => {
        const isFs = !!document.fullscreenElement;
        const textFs = document.getElementById('modal-text-fs');
        if (textFs) {
            textFs.innerText = isFs ? '✖ Keluar' : '⛶ Layar Penuh';
        }
        applyModalCardDimensions();
    });

    window.addEventListener('resize', () => {
        const modal = document.getElementById('materi-preview-modal');
        if (modal && !modal.classList.contains('hidden')) {
            applyModalCardDimensions();
        }
    });
        }
    });
</script>
@endpush
@endsection

