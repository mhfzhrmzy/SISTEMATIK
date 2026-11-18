<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TtsApiTest extends TestCase
{
    public function test_tts_validasi_text_wajib_diisi(): void
    {
        $response = $this->postJson('/api/tts', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['text']);
    }

    public function test_tts_validasi_text_maksimal_2000_karakter(): void
    {
        $response = $this->postJson('/api/tts', [
            'text' => str_repeat('a', 2001),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['text']);
    }

    public function test_tts_mengembalikan_file_dari_cache_jika_sudah_ada(): void
    {
        Storage::fake('public');
        $text = 'Halo selamat belajar Python.';
        $voiceId = config('services.elevenlabs.voice_id') ?: 'pNInz6obpgDQGcFmaJgB';
        $cacheKey = 'tts/'.md5($text.$voiceId).'.mp3';

        Storage::disk('public')->put($cacheKey, 'dummy-audio-content');

        $response = $this->postJson('/api/tts', [
            'text' => $text,
        ]);

        $response->assertOk()
            ->assertHeader('Content-Type', 'audio/mpeg');
    }

    public function test_tts_gagal_jika_api_key_tidak_dikonfigurasi(): void
    {
        Storage::fake('public');
        config(['services.elevenlabs.key' => null]);
        putenv('ELEVENLABS_API_KEY=');

        $response = $this->postJson('/api/tts', [
            'text' => 'Teks baru tanpa cache.',
        ]);

        $response->assertStatus(500)
            ->assertJsonPath('message', 'ElevenLabs API key tidak dikonfigurasi di file .env.');
    }

    public function test_tts_berhasil_generate_dan_simpan_cache_saat_api_key_tersedia(): void
    {
        Storage::fake('public');
        config(['services.elevenlabs.key' => 'fake-api-key-test']);

        Http::fake([
            'https://api.elevenlabs.io/*' => Http::response('fake-binary-audio-mp3', 200, [
                'Content-Type' => 'audio/mpeg',
            ]),
        ]);

        $text = 'Materi ini membahas fungsi print pada Python.';
        $response = $this->postJson('/api/tts', [
            'text' => $text,
        ]);

        $response->assertOk()
            ->assertHeader('Content-Type', 'audio/mpeg');

        $voiceId = config('services.elevenlabs.voice_id') ?: 'pNInz6obpgDQGcFmaJgB';
        $cacheKey = 'tts/'.md5($text.$voiceId).'.mp3';
        $this->assertTrue(Storage::disk('public')->exists($cacheKey));
    }
}
