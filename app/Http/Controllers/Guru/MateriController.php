<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AdminGuru;
use App\Models\Materi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MateriController extends Controller
{
    /**
     * Menampilkan daftar materi di halaman Guru.
     */
    public function index(): View
    {
        $materis = Materi::with('adminGuru')->latest()->get();
        $admins = AdminGuru::orderBy('nama_lengkap', 'asc')->get();

        return view('guru.materi.index', compact('materis', 'admins'));
    }

    /**
     * Menyimpan materi baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'judul_materi' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9 ]+$/',
                Rule::unique('materi', 'judul_materi')
                    ->where('kelas', $request->input('kelas'))
                    ->where('jurusan', $request->input('jurusan')),
            ],
            'nip' => 'nullable|exists:admin_guru,nip',
            'kelas' => 'required|in:10,11,12',
            'jurusan' => 'required|in:Teknik Alat Berat,Teknik Kendaraan Ringan,Teknik Sepeda Motor,Teknik Pemesinan,Teknik Instalasi Listrik,Teknik Pembangkit Listrik,Teknik Mekatronika,Teknik Audio Video,Teknik Komputer & Jaringan,Teknik Konstruksi & Perumahan,Desain Permodelan & Informasi Bangunan,Desain Komunikasi Visual',
            'isi_materi' => 'nullable|string',
            'upload_file' => 'required|mimes:pdf,pptx,ppt,doc,docx|max:25600',
        ], [
            'judul_materi.required' => 'Judul materi wajib diisi.',
            'judul_materi.regex' => 'Judul materi hanya boleh berisi huruf dan angka.',
            'judul_materi.unique' => 'Judul materi sudah ada untuk kelas dan jurusan yang sama.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'jurusan.required' => 'Jurusan wajib dipilih.',
            'upload_file.required' => 'File modul materi wajib diupload.',
            'upload_file.mimes' => 'Format file harus PDF, PPT, PPTX, DOC, atau DOCX.',
            'upload_file.max' => 'Ukuran file tidak boleh lebih dari 25 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('upload_file')) {
            $filePath = $request->file('upload_file')->store('materi', 'public');
        }

        $nip = $request->input('nip') ?: Auth::guard('admin')->id();

        Materi::create([
            'judul_materi' => $request->judul_materi,
            'nip' => $nip,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'isi_materi' => $request->isi_materi ?? '',
            'upload_file' => $filePath,
        ]);

        return redirect()->route('materi.index')->with('success', 'Materi berhasil ditambahkan!');
    }

    /**
     * Menampilkan atau mengunduh file materi langsung untuk Guru.
     */
    public function show(Materi $materi): BinaryFileResponse|RedirectResponse
    {
        if ($materi->upload_file && Storage::disk('public')->exists($materi->upload_file)) {
            return response()->file(Storage::disk('public')->path($materi->upload_file));
        }

        return redirect()->route('materi.index')->with('error', 'File materi tidak ditemukan.');
    }

    /**
     * Menampilkan form edit materi.
     */
    public function edit(Materi $materi): View
    {
        $admins = AdminGuru::orderBy('nama_lengkap', 'asc')->get();

        return view('guru.materi.edit', compact('materi', 'admins'));
    }

    /**
     * Memperbarui data materi yang sudah ada.
     */
    public function update(Request $request, Materi $materi): RedirectResponse
    {
        $request->validate([
            'judul_materi' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9 ]+$/',
                Rule::unique('materi', 'judul_materi')
                    ->ignore($materi->id_materi, 'id_materi')
                    ->where('kelas', $request->input('kelas'))
                    ->where('jurusan', $request->input('jurusan')),
            ],
            'nip' => 'nullable|exists:admin_guru,nip',
            'kelas' => 'required|in:10,11,12',
            'jurusan' => 'required|in:Teknik Alat Berat,Teknik Kendaraan Ringan,Teknik Sepeda Motor,Teknik Pemesinan,Teknik Instalasi Listrik,Teknik Pembangkit Listrik,Teknik Mekatronika,Teknik Audio Video,Teknik Komputer & Jaringan,Teknik Konstruksi & Perumahan,Desain Permodelan & Informasi Bangunan,Desain Komunikasi Visual',
            'isi_materi' => 'nullable|string',
            'upload_file' => 'nullable|mimes:pdf,pptx,ppt,doc,docx|max:25600',
        ], [
            'judul_materi.required' => 'Judul materi wajib diisi.',
            'judul_materi.regex' => 'Judul materi hanya boleh berisi huruf dan angka.',
            'judul_materi.unique' => 'Judul materi sudah ada untuk kelas dan jurusan yang sama.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'jurusan.required' => 'Jurusan wajib dipilih.',
        ]);

        $data = [
            'judul_materi' => $request->judul_materi,
            'nip' => $request->input('nip') ?: $materi->nip,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'isi_materi' => $request->isi_materi ?? '',
        ];

        if ($request->hasFile('upload_file')) {
            if ($materi->upload_file && Storage::disk('public')->exists($materi->upload_file)) {
                Storage::disk('public')->delete($materi->upload_file);
            }
            $data['upload_file'] = $request->file('upload_file')->store('materi', 'public');
        }

        $materi->update($data);

        return redirect()->route('materi.index')->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Menghapus materi beserta file lampirannya.
     */
    public function destroy(Materi $materi): RedirectResponse
    {
        if ($materi->upload_file && Storage::disk('public')->exists($materi->upload_file)) {
            Storage::disk('public')->delete($materi->upload_file);
        }

        $materi->delete();

        return redirect()->route('materi.index')->with('success', 'Materi berhasil dihapus!');
    }
}
