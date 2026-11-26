@extends('layouts.app')

@section('title', 'Penerbitan Sertifikat')

@section('content')
    @include('partials.sertifikat.index-content', [
        'routePrefix' => 'sertifikat',
        'isSuperadmin' => false,
    ])
@endsection
