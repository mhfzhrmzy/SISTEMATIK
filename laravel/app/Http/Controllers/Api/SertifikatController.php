<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SertifikatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $siswa = $request->user();

        $sertifikat = Sertifikat::where('nisn', $siswa->nisn)
            ->with(['materi.Guru', 'kategori'])
            ->latest()
            ->get()
            ->map(function (Sertifikat $item) {
                $guru = $item->materi?->Guru;

                return [
                    'id_sertifikat' => $item->id_sertifikat,
                    'judul_sertifikat' => $item->judul_sertifikat,
                    'kategori' => $item->kategori?->nama_kategori,
                    'penerbit' => $item->penerbit,
                    'deskripsi' => $item->deskripsi,
                    'tanggal_terbit' => $item->tanggal_terbit,
                    'tipe_sertifikat' => $item->tipe_sertifikat,
                    'terverifikasi' => $item->terverifikasi,
                    'file_url' => $item->file_sertifikat
                        ? Storage::disk('public')->url($item->file_sertifikat)
                        : null,
                    'badge_url' => $item->badge_url
                        ? Storage::disk('public')->url($item->badge_url)
                        : null,
                    'guru' => $guru
                        ? ['nip' => $guru->nip, 'nama_lengkap' => $guru->nama_lengkap]
                        : null,
                    'materi' => $item->materi
                        ? ['id_materi' => $item->materi->id_materi, 'judul_materi' => $item->materi->judul_materi]
                        : null,
                ];
            });

        return response()->json([
            'message' => 'Daftar sertifikat berhasil diambil.',
            'data' => $sertifikat,
        ]);
    }

    public function download(Request $request, Sertifikat $sertifikat): JsonResponse|Response
    {
        if ($sertifikat->nisn !== $request->user()->nisn) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sertifikat ini.',
            ], 404);
        }

        if (! $sertifikat->file_sertifikat || ! Storage::disk('public')->exists($sertifikat->file_sertifikat)) {
            return response()->json([
                'message' => 'File sertifikat tidak ditemukan.',
            ], 404);
        }

        return response()->file(Storage::disk('public')->path($sertifikat->file_sertifikat));
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul_sertifikat' => ['required', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'tanggal_terbit' => ['nullable', 'date'],
            'deskripsi' => ['nullable', 'string'],
            'file_sertifikat' => ['required', 'file', 'mimetypes:application/pdf', 'max:2048'],
        ]);

        $siswa = $request->user();
        $path = $request->file('file_sertifikat')->store("sertifikat/siswa/{$siswa->nisn}", 'public');

        $sertifikat = Sertifikat::create([
            'nisn' => $siswa->nisn,
            'id_materi' => null,
            'judul_sertifikat' => $validated['judul_sertifikat'],
            'penerbit' => $validated['penerbit'] ?? null,
            'tanggal_terbit' => $validated['tanggal_terbit'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_sertifikat' => $path,
            'tipe_sertifikat' => 'mandiri',
        ]);

        return response()->json([
            'message' => 'Sertifikat berhasil diunggah.',
            'data' => [
                'id_sertifikat' => $sertifikat->id_sertifikat,
                'judul_sertifikat' => $sertifikat->judul_sertifikat,
                'penerbit' => $sertifikat->penerbit,
                'tanggal_terbit' => $sertifikat->tanggal_terbit,
                'file_url' => Storage::disk('public')->url($path),
            ],
        ], 201);
    }

    public function destroy(Request $request, Sertifikat $sertifikat): JsonResponse
    {
        if ($sertifikat->nisn !== $request->user()->nisn) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sertifikat ini.',
            ], 404);
        }

        if ($sertifikat->tipe_sertifikat !== 'mandiri') {
            return response()->json([
                'message' => 'Sertifikat dari guru tidak dapat dihapus.',
            ], 403);
        }

        if ($sertifikat->file_sertifikat && Storage::disk('public')->exists($sertifikat->file_sertifikat)) {
            Storage::disk('public')->delete($sertifikat->file_sertifikat);
        }

        $sertifikat->delete();

        return response()->json([
            'message' => 'Sertifikat berhasil dihapus.',
        ]);
    }
}
