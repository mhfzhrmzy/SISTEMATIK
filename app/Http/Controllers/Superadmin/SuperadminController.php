<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AdminGuru;
use App\Models\Materi;
use App\Models\PenggunaSiswa;
use App\Models\Quiz;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperadminController extends Controller
{
    /**
     * Menampilkan dashboard utama superadmin beserta data guru dan siswa.
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $menu = $request->query('menu', 'dashboard');

        if (! in_array($menu, ['dashboard', 'guru', 'siswa'], true)) {
            $menu = 'dashboard';
        }

        $guruQuery = AdminGuru::orderBy('nama_lengkap');
        if ($search !== '') {
            $guruQuery->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }
        $gurus = $guruQuery->get();

        $siswaQuery = PenggunaSiswa::orderBy('nama_lengkap');
        if ($search !== '') {
            $siswaQuery->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }
        $siswas = $siswaQuery->get();

        $materiCount = Materi::count();
        $quizCount = Quiz::count();
        $sertifikatCount = Sertifikat::count();
        $superadmin = Auth::guard('superadmin')->user();

        return view('superadmin.index', compact(
            'gurus',
            'siswas',
            'materiCount',
            'quizCount',
            'sertifikatCount',
            'menu',
            'superadmin',
            'search'
        ));
    }
}
