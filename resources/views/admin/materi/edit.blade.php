@extends('layouts.app')
@section('title', 'Edit Materi')

@section('content')
    <h1>Edit Materi</h1>

    <form action="{{ route('admin.materi.update', $materi) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="field">
            <label>Judul Materi</label>
            <input type="text" name="judul" value="{{ old('judul', $materi->judul) }}" required>
        </div>

        <div class="field">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
        </div>

        <div class="field">
            <label>File Modul Saat Ini</label>
            @if($materi->file_path)
                <p><a href="{{ asset('storage/'.$materi->file_path) }}" target="_blank">{{ $materi->file_name }}</a></p>
            @else
                <p><em>Belum ada file</em></p>
            @endif
            <label>Ganti File (opsional)</label>
            <input type="file" name="file_modul">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection
