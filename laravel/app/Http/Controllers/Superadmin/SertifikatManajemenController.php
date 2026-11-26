<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Concerns\ManagesSertifikatPenerbitan;
use App\Http\Controllers\Controller;

class SertifikatManajemenController extends Controller
{
    use ManagesSertifikatPenerbitan;

    protected function sertifikatViewPrefix(): string
    {
        return 'superadmin.sertifikat';
    }

    protected function sertifikatIndexRoute(): string
    {
        return 'superadmin.sertifikat.index';
    }

    protected function sertifikatSessionPrefix(): string
    {
        return 'superadmin_sertifikat';
    }

    protected function sertifikatStorageFolder(): string
    {
        return 'sertifikat/superadmin';
    }
}
