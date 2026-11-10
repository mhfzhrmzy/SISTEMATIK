<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Quiz System</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; margin: 0; padding: 2rem; }
        .container { max-width: 1100px; margin: 0 auto; background: #ffffff; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem; }
        .alert { padding: 0.85rem 1rem; border-radius: 6px; margin-bottom: 1.25rem; font-size: 0.95rem; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .form-card { background: #f1f5f9; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem; color: #475569; }
        .form-group input, .form-group select, .form-group textarea { padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; }
        .btn { padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background 0.2s; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: #fff; border-radius: 8px; overflow: hidden; }
        th, td { border: 1px solid #e2e8f0; padding: 0.85rem; text-align: left; font-size: 0.9rem; }
        th { background-color: #f8fafc; font-weight: 600; color: #334155; }
        .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        .badge-pre { background: #e0e7ff; color: #3730a3; }
        .badge-post { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>

<div class="container">
    <h2>Kelola Data Quiz</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="form-card">
        <h3 style="margin-top:0; font-size:1.1rem;">Tambah Quiz Baru</h3>
        <form action="{{ route('quiz.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>ID Quiz</label>
                    <input type="text" name="id_quiz" placeholder="Contoh: QZ001" required value="{{ old('id_quiz') }}">
                </div>
                <div class="form-group">
                    <label>Guru (NIP)</label>
                    <select name="nip" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->nip }}" {{ old('nip') == $guru->nip ? 'selected' : '' }}>
                                {{ $guru->nama_lengkap }} ({{ $guru->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Materi</label>
                    <select name="id_materi" required>
                        <option value="">-- Pilih Materi --</option>
                        @foreach($materis as $materi)
                            <option value="{{ $materi->id_materi }}" {{ old('id_materi') == $materi->id_materi ? 'selected' : '' }}>
                                {{ $materi->judul_materi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipe Test</label>
                    <select name="tipe_test" required>
                        <option value="pre_test" {{ old('tipe_test') == 'pre_test' ? 'selected' : '' }}>Pre Test</option>
                        <option value="post_test" {{ old('tipe_test') == 'post_test' ? 'selected' : '' }}>Post Test</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Poin</label>
                    <input type="number" name="poin" min="0" required value="{{ old('poin', 100) }}">
                </div>
                <div class="form-group">
                    <label>Timer (Menit)</label>
                    <input type="number" name="timer" min="1" required value="{{ old('timer', 30) }}">
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ old('tanggal') }}">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Kunci Jawaban</label>
                <textarea name="kunci_jawaban" rows="2" placeholder="Masukkan kunci jawaban..." required>{{ old('kunci_jawaban') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Quiz</button>
        </form>
    </div>

    <h3>Daftar Quiz</h3>
    <table>
        <thead>
            <tr>
                <th>ID Quiz</th>
                <th>Guru / NIP</th>
                <th>Materi</th>
                <th>Tipe</th>
                <th>Poin</th>
                <th>Timer</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quizzes as $q)
                <tr>
                    <td><strong>{{ $q->id_quiz }}</strong></td>
                    <td>{{ $q->guru->nama_lengkap ?? $q->nip }}</td>
                    <td>{{ $q->materi->judul_materi ?? $q->id_materi }}</td>
                    <td>
                        <span class="badge {{ $q->tipe_test == 'pre_test' ? 'badge-pre' : 'badge-post' }}">
                            {{ str_replace('_', ' ', $q->tipe_test) }}
                        </span>
                    </td>
                    <td>{{ $q->poin }}</td>
                    <td>{{ $q->timer }} Menit</td>
                    <td>{{ $q->tanggal }}</td>
                    <td>
                        <form action="{{ route('quiz.destroy', $q->id_quiz) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus quiz ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748b;">Belum ada data quiz.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $quizzes->links() }}
    </div>
</div>

</body>
</html>
