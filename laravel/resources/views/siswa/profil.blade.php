@extends('layouts.app')

@section('title', 'Profil & Portofolio Siswa')

@section('content')
<div class="space-y-6">

    <div class="border-b border-gray-100 pb-4 mb-6">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide bg-[#7B1038] text-white mb-2 shadow-2xs">
            AKUN SAYA
        </span>
        <h1 class="text-3xl font-bold text-[#1F2937] mb-1">Profil &amp; Informasi Siswa</h1>
        <p class="text-base text-gray-600">Kelola informasi data diri, foto profil, dan kredensial keamanan akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full">

        {{-- Kartu profil --}}
        <x-card class="lg:col-span-4 text-center space-y-4 h-fit">
            @if ($siswa->foto_profile)
                <img src="{{ asset('storage/' . $siswa->foto_profile) }}" alt="{{ $siswa->nama_lengkap }}"
                     class="w-24 h-24 mx-auto rounded-full object-cover shadow-xs border-2 border-white">
            @else
                <div class="w-24 h-24 mx-auto rounded-full bg-[#7B1038] text-white flex items-center justify-center font-bold text-2xl shadow-xs">
                    {{ strtoupper(substr($siswa->nama_lengkap, 0, 3)) }}
                </div>
            @endif

            {{-- Tombol foto --}}
            <div class="flex justify-center gap-2">
                <form action="{{ route('siswa.profil.foto') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="cursor-pointer text-sm font-semibold bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl px-4 py-2 inline-block shadow-xs transition">
                        {{ $siswa->foto_profile ? 'Ubah Foto' : 'Upload Foto' }}
                        <input type="file" name="foto" accept="image/png,image/jpeg" class="hidden"
                               onchange="this.form.submit()">
                    </label>
                </form>

                @if ($siswa->foto_profile)
                    <form action="{{ route('siswa.profil.foto.hapus') }}" method="POST"
                          onsubmit="return confirm('Hapus foto profil?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-semibold bg-gray-100 hover:bg-rose-50 text-rose-600 rounded-xl px-4 py-2 transition">
                            Hapus Foto
                        </button>
                    </form>
                @endif
            </div>

            <div>
                <h2 class="text-lg font-semibold text-[#1F2937]">{{ $siswa->nama_lengkap }}</h2>
                <p class="text-sm text-gray-600 font-mono mt-0.5">NISN: {{ $siswa->nisn }}</p>
            </div>

            <div class="pt-2 border-t border-gray-100 space-y-2 text-sm">
                <div class="flex justify-between items-center text-gray-600">
                    <span>Bergabung Sejak</span>
                    <span class="font-semibold text-[#1F2937]">{{ $siswa->created_at->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </x-card>

        {{-- Data pokok --}}
        <x-card class="lg:col-span-8 space-y-4">
            <h3 class="text-lg font-semibold text-[#1F2937]">Informasi Akademik</h3>

            @foreach ([
                'NISN'               => $siswa->nisn,
                'Nama Lengkap Siswa' => $siswa->nama_lengkap,
                'Kelas'              => 'Kelas ' . ($siswa->kelas ?? '-'),
                'Jurusan'            => $siswa->jurusan ?? '-',
            ] as $label => $nilai)
                <div class="space-y-1.5">
                    <p class="text-sm font-semibold text-gray-600">{{ $label }}</p>
                    <div class="bg-gray-50 rounded-xl px-4 py-2.5 text-sm font-semibold text-[#1F2937]">{{ $nilai }}</div>
                </div>
            @endforeach

            <div class="pt-2">
                <button type="button" onclick="document.getElementById('formPassword').classList.toggle('hidden')"
                        class="inline-flex items-center text-sm font-semibold bg-slate-100 hover:bg-[#7B1038] hover:text-white text-[#7B1038] border border-slate-200 rounded-xl px-4 py-2.5 transition">
                    <span>Ganti Kata Sandi</span>
                </button>
            </div>

            <form id="formPassword" action="{{ route('siswa.profil.password') }}" method="POST" autocomplete="off"
                  class="hidden border-t border-gray-100 pt-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-600">Password Lama</label>
                    <div class="flex gap-2">
                        <input type="password" id="pwLama" name="password_lama" required
                               autocomplete="current-password"
                               placeholder="Masukkan kata sandi saat ini"
                               value=""
                               class="flex-1 bg-gray-50 border-0 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7B1038] transition">
                        <button type="button" onclick="togglePw('pwLama', this)" class="px-2 text-gray-500 hover:text-[#7B1038]" aria-label="Tampilkan password">
                            <svg class="icon-tutup w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M4 4l16 16"/>
                            </svg>
                            <svg class="icon-buka w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-600">Password Baru (min. 8 karakter)</label>
                    <div class="flex gap-2">
                        <input type="password" id="pwBaru" name="password_baru" required minlength="8"
                               autocomplete="new-password"
                               placeholder="Masukkan minimal 8 karakter password baru"
                               value=""
                               class="flex-1 bg-gray-50 border-0 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7B1038] transition">
                        <button type="button" onclick="togglePw('pwBaru', this)" class="px-2 text-gray-500 hover:text-[#7B1038]" aria-label="Tampilkan password">
                            <svg class="icon-tutup w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M4 4l16 16"/>
                            </svg>
                            <svg class="icon-buka w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex gap-2 pt-1">
                    <button type="submit" class="text-sm font-semibold bg-[#7B1038] hover:bg-[#5E0C2B] text-white rounded-xl px-5 py-2.5 shadow-xs transition">
                        Simpan Password
                    </button>
                    <button type="button" onclick="batalPassword()"
                            class="text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-[#5E0C2B] rounded-xl px-4 py-2.5 transition">
                        Batal
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        function togglePw(id, tombol) {
            const input = document.getElementById(id);
            const tampil = input.type === 'password';

            input.type = tampil ? 'text' : 'password';

            tombol.querySelector('.icon-buka').classList.toggle('hidden', !tampil);
            tombol.querySelector('.icon-tutup').classList.toggle('hidden', tampil);
        }

        function batalPassword() {
            const form = document.getElementById('formPassword');

            form.reset();
            const pwLama = document.getElementById('pwLama');
            const pwBaru = document.getElementById('pwBaru');
            if (pwLama) pwLama.value = '';
            if (pwBaru) pwBaru.value = '';
            form.querySelectorAll('input[type="text"]').forEach(i => i.type = 'password');
            form.querySelectorAll('.icon-buka').forEach(i => i.classList.add('hidden'));
            form.querySelectorAll('.icon-tutup').forEach(i => i.classList.remove('hidden'));

            form.classList.add('hidden');
        }
    </script>
</div>
@endsection
