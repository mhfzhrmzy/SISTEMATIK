<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Concerns\ManagesSertifikatPenerbitan;
use App\Http\Controllers\Controller;

class SertifikatController extends Controller
{
    use ManagesSertifikatPenerbitan;

    protected function sertifikatViewPrefix(): string
    {
        return 'guru.sertifikat';
    }

    protected function sertifikatIndexRoute(): string
    {
        return 'sertifikat.index';
    }

    protected function sertifikatSessionPrefix(): string
    {
        return 'sertifikat';
    }

    protected function sertifikatStorageFolder(): string
    {
        return 'sertifikat/guru';
    }
}
