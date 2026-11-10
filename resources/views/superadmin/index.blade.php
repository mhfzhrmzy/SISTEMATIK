@extends('layouts.app')

@section('title', 'Superadmin Portal — SMKN 2 Jember')

@section('content')
<div class="flex min-h-screen bg-slate-100 font-sans text-slate-800">
    <!-- LEFT SIDEBAR -->
    @include('superadmin.partials.sidebar')

    <!-- MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        @include('superadmin.partials.header')

        <!-- PAGE CONTENT -->
        <main class="p-6 lg:p-8 space-y-6">
            <!-- Flash Session Alerts -->
            @if (session('success'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 text-sm font-semibold space-y-1 shadow-sm">
                    <p class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Terjadi kesalahan pada input:
                    </p>
                    <ul class="list-disc list-inside text-xs space-y-0.5 pl-6 font-normal">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($menu === 'dashboard')
                @include('superadmin.partials.dashboard')
            @elseif ($menu === 'guru')
                @include('superadmin.daftarGuru.index')
            @elseif ($menu === 'siswa')
                @include('superadmin.daftarSiswa.index')
            @endif
        </main>
    </div>
</div>

<!-- MODALS & SCRIPTS -->
@include('superadmin.partials.modals')
@endsection