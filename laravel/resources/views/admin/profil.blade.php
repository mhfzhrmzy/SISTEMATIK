@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="mb-6">
    <span class="inline-block bg-[#7B1038]/10 text-[#7B1038] text-[10px] font-bold px-2.5 py-0.5 rounded-full">AKUN SAYA</span>
    <h1 class="text-2xl font-bold mt-1.5">Profil &amp; Informasi Guru</h1>
    <p class="text-sm text-gray-500 mt-0.5">Kelola informasi data diri, foto profil, dan kredensial keamanan akun Anda.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    {{-- KIRI: foto & ringkasan --}}
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.04)] p-6 text-center lg:col-span-1">
        @if($admin->foto_profile)
            <img src="{{ asset('storage/'.$admin->foto_profile) }}" class="w-[86px] h-[86px] rounded-full object-cover mx-auto" alt="Foto">
        @else
            <div class="w-[86px] h-[86px] rounded-full bg-[#7B1038]/10 text-[#7B1038] mx-auto flex items-center justify-center text-2xl font-bold">
                {{ strtoupper(substr($admin->nama_lengkap, 0, 3)) }}
            </div>
        @endif

        <div class="mt-3 flex items-center justify-center gap-2">
            {{-- Ubah / Upload Foto --}}
            <form action="{{ route('admin.profil.foto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="foto" id="foto" accept="image/*" class="hidden" onchange="this.form.submit()">
                <label for="foto" class="cursor-pointer inline-block bg-[#7B1038] hover:bg-[#5E0C2B] transition text-white text-xs font-semibold px-4 py-1.5 rounded-full">
                    {{ $admin->foto_profile ? 'Ubah Foto' : 'Upload Foto' }}
                </label>
            </form>

            {{-- Hapus Foto (hanya tampil jika sudah ada foto) --}}
            @if($admin->foto_profile)
                <form action="{{ route('admin.profil.foto.hapus') }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus foto profil?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-100 hover:bg-red-600 hover:text-white transition text-red-600 text-xs font-semibold px-4 py-1.5 rounded-full">
                        Hapus Foto
                    </button>
                </form>
            @endif
        </div>

        <h2 class="font-semibold text-lg mt-4">{{ $admin->nama_lengkap }}</h2>
        <p class="text-xs text-gray-500 mt-0.5">NIP: {{ $admin->nip }}</p>

        <div class="mt-5 rounded-xl bg-[#7B1038]/5 py-3 px-4">
            <p class="text-[11px] font-semibold text-[#7B1038]">Status Akun</p>
            <p class="text-lg font-bold text-[#5E0C2B]">Guru Aktif</p>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between text-xs text-gray-500">
            <span>Bergabung Sejak</span>
            <span class="text-gray-800 font-semibold">{{ $admin->created_at?->translatedFormat('F Y') ?? '-' }}</span>
        </div>
    </div>

    {{-- KANAN: informasi pribadi --}}
    <div class="bg-white rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.04)] p-6 lg:col-span-2">
        <h3 class="font-semibold text-base mb-4">Informasi Pribadi Guru</h3>

        <label class="block text-xs text-gray-500">NIP</label>
        <div class="bg-gray-50 rounded-lg px-4 py-3 text-sm mt-1 mb-4">{{ $admin->nip }}</div>

        <label class="block text-xs text-gray-500">Nama Lengkap Guru</label>
        <div class="bg-gray-50 rounded-lg px-4 py-3 text-sm mt-1 mb-4">{{ $admin->nama_lengkap }}</div>

        <button type="button" onclick="document.getElementById('form-password').classList.toggle('hidden')"
                class="bg-gray-100 hover:bg-[#7B1038]/10 transition text-xs font-semibold px-4 py-2.5 rounded-lg">
            🔑 Ganti Kata Sandi
        </button>

        <form id="form-password" action="{{ route('admin.profil.password') }}" method="POST"
              class="{{ $errors->has('password_lama') || $errors->has('password_baru') ? '' : 'hidden' }} mt-4 border-t border-gray-100 pt-4">
            @csrf

            @foreach([['password_lama', 'Password Lama'], ['password_baru', 'Password Baru (min. 8 karakter)']] as [$name, $label])
                <label class="block text-xs text-gray-500 {{ $loop->first ? '' : 'mt-4' }}">{{ $label }}</label>
                <div class="flex items-center gap-2 mt-1">
                    <input type="password" name="{{ $name }}" class="w-full bg-gray-50 rounded-lg px-4 py-3 text-sm border border-transparent focus:border-[#7B1038] focus:outline-none">
                    <button type="button" onclick="togglePw(this)" class="text-gray-700" aria-label="Tampilkan/sembunyikan password">
                        {{-- Mata dicoret (default, password tersembunyi) --}}
                        <svg class="icon-hide w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                        {{-- Mata biasa (password terlihat) --}}
                        <svg class="icon-show w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            @endforeach

            <div class="flex gap-2 mt-5">
                <button class="bg-[#7B1038] hover:bg-[#5E0C2B] transition text-white text-xs font-semibold px-4 py-2 rounded-lg">Simpan Password</button>
                <button type="button" onclick="document.getElementById('form-password').classList.add('hidden')"
                        class="bg-gray-100 hover:bg-gray-200 transition text-xs font-semibold px-4 py-2 rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function togglePw(btn) {
    const input = btn.parentElement.querySelector('input');
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('.icon-hide').classList.toggle('hidden', isHidden);
    btn.querySelector('.icon-show').classList.toggle('hidden', !isHidden);
}
</script>
@endpush
@endsection