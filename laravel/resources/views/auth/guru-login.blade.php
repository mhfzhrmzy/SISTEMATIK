@extends('layouts.app')
@section('title', 'Login Guru')
@section('content')
<div class="min-h-screen w-full flex flex-col md:flex-row bg-[#6D1837] relative overflow-hidden font-sans">

    <!-- LEFT AREA: Deep Blue Branding & Title Section -->
    <div class="hidden md:flex md:w-[52%] lg:w-[56%] min-h-screen bg-gradient-to-br from-[#7E2043] via-[#6D1837] to-[#551229] flex-col justify-center px-12 lg:px-20 text-white relative">
        <div class="max-w-xl relative z-10">
        </div>
    </div>

    <!-- RIGHT AREA: White Curved Form Container -->
    <div class="relative w-full md:w-[48%] lg:w-[44%] min-h-screen flex items-center z-10">
        <!-- Secondary Stacked Blue Backdrop Layer -->

        <!-- Primary White Card -->
        <div class="w-full h-full min-h-screen bg-white shadow-2xl px-6 sm:px-12 lg:px-16 py-12 flex flex-col justify-between relative z-10">

            <!-- Main Form Section -->
            <div class="my-auto py-8">
                <!-- Title -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-black tracking-tight">Login Guru</h2>
                    <p class="text-xs text-slate-700 mt-1">Masuk ke Portal Pendidik</p>
                </div>

                <!-- Role Selector Buttons -->
                <div class="p-1 bg-slate-100 flex gap-1 mb-6 border border-slate-200/80">
                    <a href="{{ route('siswa.login') }}" class="flex-1 py-2.5 px-3 text-xs font-semibold text-center transition text-slate-600 hover:text-slate-900">
                        Siswa (NISN)
                    </a>
                    <a href="{{ route('admin.login') }}" class="flex-1 py-2.5 px-3 text-xs font-semibold text-center transition bg-white text-[#6D1837] shadow-sm border border-slate-200">
                        Guru / Pendidik
                    </a>
                </div>

                <!-- Alerts -->
                @if (session('success'))
                    <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="font-medium">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('admin.login.attempt') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nomor Induk Pegawai (NIP)</label>
                        <input type="text" name="nip" value="{{ old('nip') }}" maxlength="18" placeholder="Masukkan 18 digit NIP Anda"
                            class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-[#6D1837] focus:border-[#6D1837] outline-none transition text-slate-800 placeholder-slate-400" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Kata Sandi</label>
                        <input type="password" name="password" placeholder="Masukkan Kata Sandi"
                            class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-[#6D1837] focus:border-[#6D1837] outline-none transition text-slate-800 placeholder-slate-400" required>
                    </div>

                    <button type="submit" class="w-full mt-2 bg-[#6D1837] hover:bg-[#57122B] active:scale-[0.99] text-white font-semibold py-3.5 px-4 shadow-md transition text-sm cursor-pointer">
                        Login
                    </button>
                </form>

                <!-- Notice -->
                <div class="mt-6 text-center text-xs text-slate-500">
                    Akun guru dibuat oleh Sekolah. Silakan hubungi Administrator jika belum memiliki akun.
                </div>
            </div>

            <!-- Footer -->
            <div class="text-xs text-slate-400 border-t border-slate-100 pt-4 text-center">
                <span>&copy; 2026 SMKN 2 Jember</span>
            </div>
        </div>
    </div>

</div>
@endsection