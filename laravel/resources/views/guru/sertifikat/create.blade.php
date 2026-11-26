@extends('layouts.app')

@section('title', 'Upload Sertifikat')

@section('content')
    @include('partials.sertifikat.create-content', [
        'routePrefix' => 'sertifikat',
        'isSuperadmin' => false,
    ])
@endsection
