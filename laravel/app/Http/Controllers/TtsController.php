<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TtsController extends Controller
{
    public function testTts()
    {
        $teksSoal = 'Apa yang dimaksud dengan Cloud Computing dalam jaringan komputer?';
        $apiKey = env('ELEVENLABS_API_KEY');
        $voiceId = env('ELEVENLABS_VOICE_ID');
        $namaFile = 'soal_tik_'.time().'.mp3';

        $response = Http::withoutVerifying()
            ->withHeaders([
                'xi-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                'text' => $teksSoal,
                'model_id' => 'eleven_multilingual_v2',
                'voice_settings' => [
                    'stability' => 0.45,
                    'similarity_boost' => 0.8,
                    'style' => 0.0,
                    'use_speaker_boost' => true,
                ],
            ]);

        if ($response->successful()) {
            if (! Storage::disk('public')->exists('audio_soal')) {
                Storage::disk('public')->makeDirectory('audio_soal');
            }

            Storage::disk('public')->put('audio_soal/'.$namaFile, $response->body());
            $urlAudio = asset('storage/audio_soal/'.$namaFile);

            return response()->json([
                'status' => 'success',
                'message' => 'Audio berhasil dibuat di localhost!',
                'text' => $teksSoal,
                'audio_url' => $urlAudio,
            ]);
        }

        // PERBAIKAN DI SINI: Menggunakan ->body() untuk menghindari crash HTTP 500 internal jika respons ElevenLabs bukan format JSON
        return response()->json([
            'status' => 'error_terdeteksi',
            'http_status_code' => $response->status(),
            'raw_body_error' => $response->body(),
        ]);
    }
}
