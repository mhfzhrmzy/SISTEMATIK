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
            ->with(['materi', 'guru'])
            ->latest()
            ->get()
            ->map(function (Sertifikat $item) {
                return [
                    'id_sertifikat' => $item->id_sertifikat,
                    'judul_sertifikat' => $item->judul_sertifikat,
                    'kategori' => $item->kategori,
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
                    'guru' => $item->guru
                        ? ['nip' => $item->guru->nip, 'nama_lengkap' => $item->guru->nama_lengkap]
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
}
