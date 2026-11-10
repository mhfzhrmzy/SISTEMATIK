@extends('layouts.app')
@section('title', 'Bahan Ajar')

@section('content')
    <h1>Bahan Ajar</h1>

    <form action="{{ route('admin.materi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="field">
            <label>Judul Materi</label>
            <input type="text" name="judul" value="{{ old('judul') }}" required>
        </div>

        <div class="field">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="field">
            <label>Upload File Modul (pdf/doc/docx/ppt/pptx, maks 10MB)</label>
            <input type="file" name="file_modul">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection
