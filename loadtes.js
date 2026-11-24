import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { SharedArray } from 'k6/data';
import { Trend } from 'k6/metrics';

const users = new SharedArray('akun_siswa', function () {
  return [
    { nisn: '0090000001', password: 'password123' }, // Siswa Load Test (TKJ 10-1)
  ];
});

export const options = {
  stages: [
    { duration: '1m', target: 50 },  // Ramp-up menuju 50 VU
    { duration: '2m', target: 100 }, // Puncak beban 100 VU
    { duration: '1m', target: 0 },   // Ramp-down
  ],
  thresholds: {
    http_req_duration: ['p(95)<3000'], // p95 < 3,0 detik
    http_req_failed: ['rate<0.01'],    // error rate < 1%
  },
};

const BASE_URL = 'http://semester3testdatabase.test/api';

const loginDuration = new Trend('login_duration', true);
const materiDuration = new Trend('materi_duration', true);
const quizDuration = new Trend('quiz_duration', true);
const leaderboardDuration = new Trend('leaderboard_duration', true);

let token = null;
let quizId = null;

function loginSiswa() {
  const user = users[(__VU - 1) % users.length];

  const loginRes = http.post(
    `${BASE_URL}/login/siswa`,
    JSON.stringify({
      nisn: user.nisn,
      password: user.password,
    }),
    {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    }
  );
  loginDuration.add(loginRes.timings.duration);

  const loginOk = check(loginRes, {
    'Login status 200': (r) => r.status === 200,
    'Token Bearer diterima': (r) => r.json('token') !== undefined,
  });

  if (!loginOk) {
    return null;
  }

  return loginRes.json('token');
}

export default function () {
  if (!token) {
    token = loginSiswa();
    if (!token) {
      sleep(1);
      return;
    }
  }

  const authHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${token}`,
  };

  group('Alur Pembelajaran Siswa', function () {
    // 1. Katalog bahan ajar
    const materiRes = http.get(`${BASE_URL}/siswa/materi`, { headers: authHeaders });
    materiDuration.add(materiRes.timings.duration);
    const materiOk = check(materiRes, { 'Status Materi 200': (r) => r.status === 200 });

    // 2. Soal pretest: ambil id dari materi milik siswa, jangan hardcode id 1.
    //    Akun yang kelasnya tidak punya materi akan melewati langkah ini.
    if (materiOk && quizId === null) {
      const data = materiRes.json('data') || [];
      const materiDenganPretest = data.find((m) => m.id_pretest);
      quizId = materiDenganPretest ? materiDenganPretest.id_pretest : 0;
    }

    if (quizId) {
      const quizRes = http.get(`${BASE_URL}/siswa/quiz/${quizId}`, { headers: authHeaders });
      quizDuration.add(quizRes.timings.duration);
      check(quizRes, { 'Status Kuis 200': (r) => r.status === 200 });
      sleep(2);
    }

    // 3. Leaderboard
    const lbRes = http.get(`${BASE_URL}/siswa/leaderboard`, { headers: authHeaders });
    leaderboardDuration.add(lbRes.timings.duration);
    check(lbRes, { 'Status Leaderboard 200': (r) => r.status === 200 });
  });

  sleep(1);
}

function formatDetik(ms) {
  if (ms === undefined || ms === null || Number.isNaN(ms)) {
    return '-';
  }
  return (ms / 1000).toFixed(2) + ' s';
}

function formatPersen(rate) {
  if (rate === undefined || rate === null || Number.isNaN(rate)) {
    return '-';
  }
  return (rate * 100).toFixed(2) + ' %';
}

function lulus(ok) {
  return ok ? 'LULUS' : 'GAGAL';
}

function p95(metric) {
  return metric && metric.values ? metric.values['p(95)'] : undefined;
}

// Ringkasan akhir kustom (gaya Indonesia) menggantikan summary bawaan k6.
export function handleSummary(data) {
  const m = data.metrics;
  const totalRequest = m.http_reqs ? m.http_reqs.values.count : 0;
  const rps = m.http_reqs ? m.http_reqs.values.rate : 0;
  const durasi = m.http_req_duration ? m.http_req_duration.values : {};
  const gagal = m.http_req_failed ? m.http_req_failed.values.rate : 0;
  const cek = m.checks ? m.checks.values.rate : 0;

  const targetP95 = 3000;
  const targetGagal = 0.01;
  const maxVus = m.vus_max ? m.vus_max.values.max : 0;

  const baris = [
    '=== Ringkasan tahap sedang (' + maxVus + ' pengguna) ===',
    '',
    'Total request        : ' + totalRequest,
    'Request per detik    : ' + rps.toFixed(1),
    'Rata-rata respons    : ' + formatDetik(durasi.avg),
    'p95 respons          : ' + formatDetik(durasi['p(95)']) + ' (target < 3 s) ' + lulus(durasi['p(95)'] < targetP95),
    'p90 respons          : ' + formatDetik(durasi['p(90)']),
    'Request gagal        : ' + formatPersen(gagal) + ' (target < 1 %) ' + lulus(gagal < targetGagal),
    'Cek berhasil         : ' + formatPersen(cek) + ' ' + lulus(cek === 1),
    '',
    'p95 Login            : ' + formatDetik(p95(m.login_duration)),
    'p95 Materi           : ' + formatDetik(p95(m.materi_duration)),
    'p95 Kuis             : ' + formatDetik(p95(m.quiz_duration)),
    'p95 Leaderboard      : ' + formatDetik(p95(m.leaderboard_duration)),
  ];

  return {
    'stdout': baris.join('\n') + '\n',
  };
}
