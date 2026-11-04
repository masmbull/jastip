<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Klien tipis untuk gateway WhatsApp self-hosted (OpenWA & yang sejenis).
 *
 * Gateway ini menghubungkan nomor WhatsApp lewat WhatsApp Web, jadi pengiriman
 * TIDAK lewat API resmi Meta. Risiko nomor dibatasi/diblokir tetap ada; lihat
 * docs/WHATSAPP-GATEWAY.md sebelum dipakai untuk pelanggan baru.
 *
 * Kontrak yang dipakai hanya dua endpoint:
 *   GET  /api/sessions/{id}                        -> status sesi
 *   POST /api/sessions/{id}/messages/send-image    -> kirim gambar + caption
 */
class WhatsappGatewayService
{
    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private string $sessionId,
        private int $timeout = 30,
    ) {}

    /**
     * Susun dari kredensial yang tersimpan di Pengaturan, dengan fallback ke
     * config/whatsapp.php (env) supaya pengembangan lokal tetap gampang.
     */
    public static function fromSettings(): self
    {
        return new self(
            rtrim((string) (setting('wa_gateway_url') ?: config('whatsapp.base_url')), '/'),
            (string) (setting('wa_gateway_key') ?: config('whatsapp.api_key')),
            (string) (setting('wa_gateway_session') ?: config('whatsapp.session_id')),
            (int) config('whatsapp.timeout', 30),
        );
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->apiKey !== '' && $this->sessionId !== '';
    }

    /**
     * Status sesi WhatsApp di gateway.
     *
     * @return array{configured: bool, connected: bool, status: string, phone: ?string, error: ?string}
     */
    public function status(): array
    {
        if (! $this->isConfigured()) {
            return [
                'configured' => false,
                'connected' => false,
                'status' => 'unconfigured',
                'phone' => null,
                'error' => 'Gateway WhatsApp belum diatur di Pengaturan.',
            ];
        }

        try {
            $response = $this->client()->get("/api/sessions/{$this->sessionId}");

            if (! $response->successful()) {
                return $this->failure('Gateway menolak permintaan (HTTP '.$response->status().').');
            }

            $data = (array) $response->json();
            $status = (string) ($data['status'] ?? 'unknown');

            return [
                'configured' => true,
                'connected' => $status === 'ready',
                'status' => $status,
                'phone' => $data['phone'] ?? null,
                'error' => $status === 'ready' ? null : $this->statusHint($status),
            ];
        } catch (\Throwable $e) {
            return $this->failure($this->transportMessage($e));
        }
    }

    /**
     * Kirim gambar (PNG invoice) dengan caption ke satu nomor.
     *
     * Gambar dikirim sebagai base64 supaya tidak bergantung pada URL publik
     * yang bisa dijangkau server gateway.
     *
     * @param  string  $pngBinary  isi file PNG mentah
     * @param  string  $caption  teks pesan (maks 1024 karakter, dipotong di sini)
     * @return array{ok: bool, error: ?string, message_id: ?string}
     */
    public function sendImage(string $phone, string $pngBinary, string $caption): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'error' => 'Gateway WhatsApp belum diatur di Pengaturan.', 'message_id' => null];
        }

        $chatId = wa_chat_id($phone);
        if ($chatId === '') {
            return ['ok' => false, 'error' => 'Nomor WhatsApp pelanggan tidak valid.', 'message_id' => null];
        }

        if ($pngBinary === '') {
            return ['ok' => false, 'error' => 'Gambar invoice kosong.', 'message_id' => null];
        }

        try {
            $response = $this->client()->post("/api/sessions/{$this->sessionId}/messages/send-image", [
                'chatId' => $chatId,
                'base64' => base64_encode($pngBinary),
                'mimetype' => 'image/png',
                'caption' => mb_substr($caption, 0, 1024),
            ]);

            if ($response->successful()) {
                return [
                    'ok' => true,
                    'error' => null,
                    'message_id' => $response->json('id') ?? $response->json('messageId'),
                ];
            }

            return [
                'ok' => false,
                'error' => $this->gatewayErrorMessage($response->status(), $response->json()),
                'message_id' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp gateway send failed', ['error' => $e->getMessage()]);

            return ['ok' => false, 'error' => $this->transportMessage($e), 'message_id' => null];
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders(['X-API-Key' => $this->apiKey])
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout);
    }

    /**
     * @return array{configured: true, connected: false, status: string, phone: null, error: string}
     */
    private function failure(string $message): array
    {
        return [
            'configured' => true,
            'connected' => false,
            'status' => 'unreachable',
            'phone' => null,
            'error' => $message,
        ];
    }

    private function statusHint(string $status): string
    {
        return match ($status) {
            'created', 'initializing' => 'Sesi WhatsApp belum dijalankan.',
            'qr_ready', 'authenticating' => 'Sesi WhatsApp menunggu scan QR di dashboard gateway.',
            'disconnected' => 'Sesi WhatsApp terputus. Buka dashboard gateway lalu hubungkan ulang.',
            'action_required' => 'WhatsApp meminta tindakan (mis. verifikasi di HP). Cek dashboard gateway.',
            'failed' => 'Sesi WhatsApp gagal. Cek dashboard gateway.',
            default => 'Status sesi WhatsApp: '.$status.'.',
        };
    }

    private function gatewayErrorMessage(int $status, mixed $body): string
    {
        $message = is_array($body) ? ($body['message'] ?? null) : null;
        $message = is_array($message) ? implode(' ', $message) : $message;

        return match ($status) {
            400 => 'Gateway menolak: '.($message ?: 'sesi tidak aktif atau permintaan tidak valid').'.',
            401, 403 => 'API key gateway ditolak. Periksa di Pengaturan.',
            404 => 'Sesi WhatsApp tidak ditemukan di gateway. Periksa Session ID.',
            default => 'Gateway gagal (HTTP '.$status.').'.($message ? ' '.$message : ''),
        };
    }

    private function transportMessage(\Throwable $e): string
    {
        return 'Tidak bisa menghubungi gateway WhatsApp: '.$e->getMessage();
    }

    /**
     * Isi Pengaturan yang menyangkut gateway (dipakai halaman Pengaturan).
     *
     * @return array<string, array{type: string, label: string}>
     */
    public static function settingDefinitions(): array
    {
        return [
            'wa_gateway_url' => ['type' => 'text', 'label' => 'URL Gateway WhatsApp'],
            'wa_gateway_key' => ['type' => 'password', 'label' => 'API Key Gateway'],
            'wa_gateway_session' => ['type' => 'text', 'label' => 'Session ID Gateway'],
        ];
    }

    /**
     * Nilai setting gateway yang aman ditampilkan (API key disamarkan).
     *
     * @return array{url: string, key_set: bool, session: string}
     */
    public static function displayConfig(): array
    {
        return [
            'url' => (string) Setting::get('wa_gateway_url', ''),
            'key_set' => (string) Setting::get('wa_gateway_key', '') !== '',
            'session' => (string) Setting::get('wa_gateway_session', ''),
        ];
    }
}
