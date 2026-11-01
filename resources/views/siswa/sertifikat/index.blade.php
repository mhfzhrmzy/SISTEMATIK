@extends('layouts.app')

@section('title', 'Sertifikat & Portofolio Siswa')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
        <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#7B1038]/10 text-[#7B1038] mb-1">
                PRESTASI BELAJAR
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F2937]">Daftar Sertifikat &amp; Portofolio</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Koleksi sertifikat kelulusan modul dan sertifikat mandiri Anda.</p>
        </div>
        <a href="{{ route('siswa.sertifikat.create') }}"
           class="inline-flex items-center gap-2 bg-[#7B1038] hover:bg-[#5E0C2B] text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl shadow-xs transition self-start sm:self-auto">
            + Upload Sertifikat Baru
        </a>
    </div>

    {{-- Grid Kartu Sertifikat --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @forelse ($sertifikat as $item)
            <div class="bg-white p-5 rounded-2xl shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $item->tipe_sertifikat === 'materi' ? 'bg-[#7B1038]/10 text-[#7B1038]' : 'bg-[#E8F8F0] text-[#1F9254]' }}">
                            {{ $item->tipe_sertifikat === 'materi' ? 'Kelulusan Modul' : 'Mandiri' }}
                        </span>
                        <span class="text-xs text-[#6B7280]">{{ $item->tanggal_terbit ?? '-' }}</span>
                    </div>
                    <h2 class="font-bold text-[#1F2937] text-base leading-snug">{{ $item->judul_sertifikat }}</h2>
                    <p class="text-xs text-[#6B7280] mt-1">Penerbit: {{ $item->penerbit ?? '-' }}</p>
                    @if ($item->materi)
                        <p class="text-xs text-[#7B1038] mt-1 font-semibold">Modul: {{ $item->materi->judul_materi }}</p>
                    @endif
                    @if ($item->deskripsi)
                        <p class="text-xs text-[#6B7280] mt-3 bg-gray-50 p-3 rounded-xl leading-relaxed">{{ $item->deskripsi }}</p>
                    @endif
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <a href="{{ asset('storage/' . $item->file_sertifikat) }}" target="_blank"
                       class="font-semibold text-[#7B1038] hover:underline">
                        📄 Lihat Berkas
                    </a>
                    @if ($item->tipe_sertifikat === 'mandiri')
                        <form action="{{ route('siswa.sertifikat.destroy', $item->id_sertifikat) }}" method="POST" onsubmit="return confirm('Hapus sertifikat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold transition">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-50 p-10 text-center text-xs sm:text-sm text-[#6B7280] rounded-2xl">
                Belum ada sertifikat yang diunggah. Klik tombol <b>+ Upload Sertifikat Baru</b> di atas untuk menambahkan berkas.
            </div>
        @endforelse
    </div>

</div>
@endsection
