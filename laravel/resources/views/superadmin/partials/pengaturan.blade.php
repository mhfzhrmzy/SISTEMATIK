<!-- PENGATURAN & LOG VIEW -->
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Pengaturan & Log Sistem</h1>
        <p class="text-xs text-slate-500 mt-1">Konfigurasi pusat, status node server, dan riwayat aktivitas superadmin.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card Status System -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Node Server & Integritas Dapodik
            </h2>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <span class="text-slate-600 font-semibold">Primary Server Node</span>
                    <span class="font-bold text-slate-900">TKJ-SRV01.SMKN2JBR</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <span class="text-slate-600 font-semibold">Status Koneksi Dapodik</span>
                    <span class="text-emerald-700 font-bold bg-emerald-100 px-2.5 py-0.5 rounded-md">Tersinkron 100%</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <span class="text-slate-600 font-semibold">Versi PHP / Framework</span>
                    <span class="font-bold text-slate-900">PHP {{ PHP_VERSION }} • Laravel {{ app()->version() }}</span>
                </div>
            </div>
        </div>

        <!-- Card Quick Backup -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                </svg>
                Pemeliharaan & Cadangan Data
            </h2>
            <p class="text-xs text-slate-500">Cadangkan basis data siswa, guru, modul materi, dan sertifikat dalam satu klik.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="alert('Backup database berhasil dibuat!')" class="bg-[#4a101d] text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-[#380b15] transition cursor-pointer">
                    💾 Buat Cadangan Database (.SQL)
                </button>
                <button type="button" onclick="alert('Log audit dibersihkan.')" class="border border-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                    🧹 Bersihkan Cache System
                </button>
            </div>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-slate-900">Riwayat Log Aktivitas Sistem</h2>
        <div class="space-y-3 text-xs">
            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100">
                <div>
                    <span class="font-bold text-slate-900 block">Sinkronisasi Otomatis Dapodik</span>
                    <span class="text-slate-500">Node TKJ-SRV01 berhasil memperbarui 42 data rombel</span>
                </div>
                <span class="text-slate-400 font-medium">Hari ini, 08:42 WIB</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100">
                <div>
                    <span class="font-bold text-slate-900 block">Verifikasi Akun Guru Pengampu</span>
                    <span class="text-slate-500">Administrator Utama menyetujui penambahan 2 akun guru baru</span>
                </div>
                <span class="text-slate-400 font-medium">Kemarin, 14:15 WIB</span>
            </div>
        </div>
    </div>
</div>
