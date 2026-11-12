<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TtsController extends Controller
{
    public function synthesize(Request $request)
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:2000'],
        ]);

        $text = $validated['text'];
        $apiKey = env('ELEVENLABS_API_KEY');
        $voiceId = env('ELEVENLABS_VOICE_ID', 'pNInz6obpgDQGcFmaJgB');

        $cacheKey = 'tts/'.md5($text.$voiceId).'.mp3';

        // Jika cache audio sudah ada di storage lokal, langsung kembalikan filenya
        if (Storage::disk('public')->exists($cacheKey)) {
            return response()->file(Storage::disk('public')->path($cacheKey), [
                'Content-Type' => 'audio/mpeg',
            ]);
        }

        if (! $apiKey) {
            return response()->json([
                'message' => 'ElevenLabs API key tidak dikonfigurasi di file .env.',
            ], 500);
        }

        try {
            // Ditambahkan ->withoutVerifying() agar aman dari pemblokiran SSL di localhost
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'xi-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                    'text' => $text,
                    'model_id' => 'eleven_multilingual_v2',
                    'voice_settings' => [
                        'stability' => 0.45,
                        'similarity_boost' => 0.8,
                        'style' => 0.0,
                        'use_speaker_boost' => true,
                    ],
                ]);

            if (! $response->successful()) {
                return response()->json([
                    'message' => 'Gagal menghasilkan audio dari ElevenLabs.',
                    'error_detail' => $response->body(), // Menggunakan ->body() agar tidak memicu HTTP 500 jika bukan JSON
                ], $response->status());
            }

            // Pastikan direktori subfolder 'tts' dibuat otomatis jika belum ada
            if (! Storage::disk('public')->exists('tts')) {
                Storage::disk('public')->makeDirectory('tts');
            }

            // Simpan file biner audio
            Storage::disk('public')->put($cacheKey, $response->body());

            return response()->file(Storage::disk('public')->path($cacheKey), [
                'Content-Type' => 'audio/mpeg',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan sistem internal di localhost.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
