@extends('layouts.app')
@section('title', 'Login Siswa')
@section('content')
<div class="min-h-screen w-full flex flex-col md:flex-row bg-white relative overflow-hidden font-sans">

    <!-- LEFT AREA: School Photo Branding & Title Section -->
    <div class="hidden md:flex md:w-[52%] lg:w-[56%] min-h-screen flex-col justify-center px-12 lg:px-20 text-white relative overflow-hidden">
        <!-- School Photo Background -->
        <img src="{{ asset('smkn2-jember.png') }}" alt="SMKN 2 Jember" class="absolute inset-0 w-full h-full object-cover z-0">
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
                    <h2 class="text-3xl font-bold text-black tracking-tight">Login</h2>
                    <p class="text-xs text-slate-700 mt-1">Masuk ke Portal Siswa SMKN 2 Jember</p>
                </div>

                <!-- Role Selector Buttons -->
                <div class="p-1 bg-slate-100 flex gap-1 mb-6 border border-slate-200/80">
                    <a href="{{ route('siswa.login') }}" class="flex-1 py-2.5 px-3 text-xs font-semibold text-center transition bg-white text-[#6D1837] shadow-sm border border-slate-200">
                        Siswa (NISN)
                    </a>
                    <a href="{{ route('admin.login') }}" class="flex-1 py-2.5 px-3 text-xs font-semibold text-center transition text-slate-600 hover:text-slate-900">
                        Guru / Pendidik
                    </a>
                </div>

                <!-- Alerts -->
                @if ($errors->any())
                    <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="font-medium">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('siswa.login.attempt') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nomor Induk Siswa Nasional (NISN)</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10" placeholder="Masukkan 10 digit NISN Anda"
                            class="w-full px-4 py-3 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-[#6D1837] focus:border-[#6D1837] outline-none transition text-slate-800 placeholder-slate-400" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" id="siswa-password-input" name="password" placeholder="Masukkan Kata Sandi"
                                class="w-full px-4 py-3 pr-11 text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:ring-2 focus:ring-[#6D1837] focus:border-[#6D1837] outline-none transition text-slate-800 placeholder-slate-400" required>
                            <button type="button" data-password-toggle="siswa-password-input" aria-label="Tampilkan kata sandi" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                <i class="password-toggle-icon fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-2 bg-[#6D1837] hover:bg-[#57122B] active:scale-[0.99] text-white font-semibold py-3.5 px-4 shadow-md transition text-sm cursor-pointer">
                        Login
                    </button>
                </form>
                <script>
                    document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
                        const input = document.getElementById(btn.dataset.passwordToggle);
                        if (!input) {
                            return;
                        }
                        btn.addEventListener('click', function () {
                            const icon = btn.querySelector('.password-toggle-icon');
                            const showing = input.type === 'text';
                            input.type = showing ? 'password' : 'text';
                            btn.setAttribute('aria-label', showing ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
                            if (icon) {
                                icon.classList.toggle('fa-eye', !showing);
                                icon.classList.toggle('fa-eye-slash', showing);
                            }
                        });
                    });
                </script>

                <!-- Notice -->
                <div class="mt-6 text-center text-xs text-slate-500">
                    Akun siswa dibuat oleh Sekolah. Silakan hubungi Administrator jika belum memiliki akun.
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
