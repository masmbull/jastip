<?php

/**
 * Gateway WhatsApp self-hosted (OpenWA atau sejenis yang bicara REST yang sama).
 *
 * Kredensial disimpan di Pengaturan (bukan .env) supaya admin bisa
 * mengubahnya tanpa menyentuh server: base URL gateway, API key, dan
 * session ID. Nilai di bawah hanya default untuk pengembangan.
 */
return [
    // URL dasar gateway, tanpa garis miring di akhir.
    // Contoh lokal: http://127.0.0.1:2785
    'base_url' => rtrim((string) env('WHATSAPP_GATEWAY_URL', ''), '/'),

    // API key admin gateway (header X-API-Key).
    'api_key' => (string) env('WHATSAPP_GATEWAY_KEY', ''),

    // ID sesi yang sudah di-scan QR-nya di dashboard gateway.
    'session_id' => (string) env('WHATSAPP_GATEWAY_SESSION', ''),

    // Batas waktu request (detik). Kirim media bisa lebih lambat dari teks.
    'timeout' => (int) env('WHATSAPP_GATEWAY_TIMEOUT', 30),
];
