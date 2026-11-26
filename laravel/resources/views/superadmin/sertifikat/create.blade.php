@extends('layouts.app')

@section('title', 'Terbitkan Sertifikat — Superadmin Portal')

@section('content')
<div class="flex min-h-screen bg-slate-100 font-sans text-slate-800">
    <!-- LEFT SIDEBAR -->
    @include('superadmin.partials.sidebar', ['menu' => 'sertifikat'])

    <!-- MAIN WORKSPACE -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        @include('superadmin.partials.header')

        <!-- PAGE CONTENT -->
        <main class="p-6 lg:p-8 space-y-6">
            @include('partials.sertifikat.create-content', [
                'routePrefix' => 'superadmin.sertifikat',
                'isSuperadmin' => true,
            ])
        </main>
    </div>
</div>
@endsection
