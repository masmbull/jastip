<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;

/**
 * Web Push (RFC 8030) with aes128gcm content encoding (RFC 8291) and
 * VAPID application-server identification (RFC 8292).
 *
 * Pure-PHP: uses only ext-openssl (EC P-256 ECDH via openssl_pkey_derive,
 * HKDF-SHA256 via hash_hmac, AES-128-GCM) + curl. No composer dependency.
 *
 * Verified against the RFC 8291 Appendix A test vector (see tests/Feature).
 */
class WebPushService
{
    public const CURVE_OID = "\x2A\x86\x48\xCE\x3D\x03\x01\x07"; // prime256v1
    public const EC_PUBLIC_OID = "\x2A\x86\x48\xCE\x3D\x02\x01";  // id-ecPublicKey

    public function __construct(private array $config) {}

    public static function fromConfig(): self
    {
        return new self((array) config('webpush'));
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['public_key']) && ! empty($this->config['private_key']);
    }

    /**
     * Encrypt a payload per RFC 8291 and POST it to a subscription endpoint.
     * Returns the push service HTTP status (0 when not attempted / encrypt failed).
     */
    public function send(PushSubscription $subscription, array $payload): int
    {
        if (! $this->isConfigured() || ! $subscription->public_key || ! $subscription->auth_token) {
            return 0;
        }

        $uaPub = $this->urlsafeDecode($subscription->public_key);
        $auth = $this->urlsafeDecode($subscription->auth_token);

        [$asPub, $asPrivObj] = $this->serverKeys();

        // Ephemeral ECDH: shared secret over the UA public key.
        $ecdh = $this->ecdh($asPrivObj, $uaPub);
        $ikm = $this->hkdfExpand(
            $this->hkdfExtract($auth, $ecdh),
            "WebPush: info\x00" . $uaPub . $asPub,
            32
        );

        $salt = random_bytes(16);
        $prk = $this->hkdfExtract($salt, $ikm);
        $cek = $this->hkdfExpand($prk, "Content-Encoding: aes128gcm\x00", 16);
        $nonce = $this->hkdfExpand($prk, "Content-Encoding: nonce\x00", 12);

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Single record: padding delimiter 0x02 appended to the plaintext.
        $tag = '';
        $cipher = openssl_encrypt($body . "\x02", 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, '', 16);
        if ($cipher === false) {
            Log::error('WebPush encrypt failed', ['endpoint' => $subscription->endpoint]);

            return 0;
        }

        // Header: salt(16) | rs(4, 4096) | idlen(1) | keyid(65, uncompressed as_public)
        $header = $salt . pack('N', 4096) . chr(strlen($asPub)) . $asPub;
        $encrypted = $header . $cipher . $tag;

        return $this->deliver($subscription->endpoint, $encrypted);
    }

    /**
     * POST the encrypted body with VAPID Authorization (RFC 8292).
     * Returns the HTTP status from the push service.
     */
    protected function deliver(string $endpoint, string $body): int
    {
        $jwt = $this->vapidToken($this->audience($endpoint));

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => (int) ($this->config['timeout'] ?? 10),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/octet-stream',
                'Content-Encoding: aes128gcm',
                'TTL: ' . (int) ($this->config['ttl'] ?? 2419200),
                'Authorization: vapid t=' . $jwt . ', k=' . $this->config['public_key'],
                'Crypto-Key: p256ecdsa=' . $this->config['public_key'],
            ],
        ]);
        curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::warning('WebPush delivery error', ['endpoint' => $endpoint, 'error' => $err]);
        }

        return $status;
    }

    /**
     * Build a signed VAPID JWT (ES256) for the given audience origin.
     */
    protected function vapidToken(string $audience): string
    {
        $header = $this->urlsafeEncode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $claims = $this->urlsafeEncode(json_encode([
            'aud' => $audience,
            'exp' => time() + 12 * 3600,
            'sub' => (string) ($this->config['subject'] ?? 'mailto:admin@example.com'),
        ]));
        $signingInput = $header . '.' . $claims;

        [$asPub, $asPrivObj] = $this->serverKeys();
        openssl_sign($signingInput, $derSig, $asPrivObj, OPENSSL_ALGO_SHA256);
        $raw = $this->derToRawSignature($derSig);

        return $signingInput . '.' . $this->urlsafeEncode($raw);
    }

    /**
     * Reconstruct the application-server EC key pair from the configured
     * raw VAPID private scalar + public point (RFC 8291 keys, not PEM).
     *
     * @return array{0: string, 1: \OpenSSLAsymmetricKey} [uncompressed public (65), private key obj]
     */
    protected function serverKeys(): array
    {
        $pub = $this->urlsafeDecode((string) $this->config['public_key']);
        $x = substr($pub, 1, 32);
        $y = substr($pub, 33, 32);
        $d = $this->urlsafeDecode((string) $this->config['private_key']);

        // PHP's openssl_pkey_new(EC) omits the public point, and
        // openssl_pkey_derive() requires the peer to expose a public key;
        // rebuild a SEC1 PEM embedding the public point so derive() works.
        $key = openssl_pkey_get_private($this->sec1Pem($d, $x, $y));
        if ($key === false) {
            throw new \RuntimeException('Invalid VAPID private key');
        }

        return [$pub, $key];
    }

    /**
     * ECDH derive (32-byte shared secret) between a private key object and a
     * raw uncompressed public point (0x04 || X || Y).
     */
    protected function ecdh(\OpenSSLAsymmetricKey $priv, string $rawPublic): string
    {
        // Peer must be a PUBLIC key object; passing a private key errors with
        // "Don't know how to get public key from this private key".
        $peer = openssl_pkey_get_public($this->spkiPem(substr($rawPublic, 1, 32), substr($rawPublic, 33, 32)));
        if ($peer === false) {
            throw new \RuntimeException('Invalid subscription (ua) public key');
        }

        $secret = openssl_pkey_derive($peer, $priv, 32);
        if ($secret === false) {
            throw new \RuntimeException('ECDH derive failed');
        }

        return $secret;
    }

    /* ------------------------------------------------------------------ */
    /* HKDF (RFC 5869) — hash_hkdf() exists but we need the raw two steps. */
    /* ------------------------------------------------------------------ */

    protected function hkdfExtract(string $salt, string $ikm): string
    {
        return hash_hmac('sha256', $ikm, $salt, true);
    }

    protected function hkdfExpand(string $prk, string $info, int $length): string
    {
        $t = '';
        $okm = '';
        for ($i = 1; strlen($okm) < $length; $i++) {
            $t = hash_hmac('sha256', $t . $info . chr($i), $prk, true);
            $okm .= $t;
        }

        return substr($okm, 0, $length);
    }

    /* ------------------------------------------------------------------ */
    /* Minimal ASN.1 DER writers for EC keys + ECDSA signature conversion. */
    /* ------------------------------------------------------------------ */

    protected function derLen(int $n): string
    {
        if ($n < 0x80) {
            return chr($n);
        }
        $b = '';
        while ($n > 0) {
            $b = chr($n & 0xFF) . $b;
            $n >>= 8;
        }

        return chr(0x80 | strlen($b)) . $b;
    }

    protected function der(string $tag, string $content): string
    {
        return $tag . $this->derLen(strlen($content)) . $content;
    }

    /** SEC1 EC PRIVATE KEY PEM with the public point embedded. */
    protected function sec1Pem(string $d, string $x, string $y): string
    {
        $body = $this->der("\x02", "\x01")
            . $this->der("\x04", $d)
            . $this->der("\xA0", $this->der("\x06", self::CURVE_OID))
            . $this->der("\xA1", $this->der("\x03", "\x00" . chr(4) . $x . $y));
        $der = $this->der("\x30", $body);

        return "-----BEGIN EC PRIVATE KEY-----\n"
            . chunk_split(base64_encode($der), 64, "\n")
            . "-----END EC PRIVATE KEY-----\n";
    }

    /** SubjectPublicKeyInfo PEM for an uncompressed EC point. */
    protected function spkiPem(string $x, string $y): string
    {
        $alg = $this->der("\x30", $this->der("\x06", self::EC_PUBLIC_OID) . $this->der("\x06", self::CURVE_OID));
        $spki = $this->der("\x30", $alg . $this->der("\x03", "\x00" . chr(4) . $x . $y));

        return "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split(base64_encode($spki), 64, "\n")
            . "-----END PUBLIC KEY-----\n";
    }

    /**
     * Convert an ECDSA DER signature (SEQUENCE of two INTEGERs) to the raw
     * 64-byte r||s form required by JWS ES256 (RFC 7515 §3.4).
     */
    protected function derToRawSignature(string $der): string
    {
        $offset = 2; // skip SEQUENCE tag + short length
        if (ord($der[1]) & 0x80) {
            $offset = 2 + (ord($der[1]) & 0x7F);
        }

        $readInt = function (int &$o) use ($der): string {
            $o++; // INTEGER tag
            $len = ord($der[$o++]);
            $val = ltrim(substr($der, $o, $len), "\x00");
            $o += $len;

            return str_pad($val, 32, "\x00", STR_PAD_LEFT);
        };

        return $readInt($offset) . $readInt($offset);
    }

    protected function audience(string $endpoint): string
    {
        $parts = parse_url($endpoint);

        return ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');
    }

    /* ------------------------------------------------------------------ */
    /* base64url helpers                                                   */
    /* ------------------------------------------------------------------ */

    public function urlsafeEncode(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    public function urlsafeDecode(string $b64): string
    {
        return base64_decode(strtr($b64, '-_', '+/') . str_repeat('=', (4 - strlen($b64) % 4) % 4));
    }

    /* ------------------------------------------------------------------ */
    /* VAPID key generation (used by `php artisan webpush:vapid`)          */
    /* ------------------------------------------------------------------ */

    /**
     * Generate a fresh VAPID key pair.
     *
     * @return array{public: string, private: string} base64url encoded
     */
    public static function generateVapidKeys(): array
    {
        $key = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'config' => self::opensslConfig(),
        ]);
        if ($key === false) {
            throw new \RuntimeException('EC key generation failed: ' . openssl_error_string());
        }

        $ec = openssl_pkey_get_details($key)['ec'];
        $svc = new self([]);

        return [
            'public' => $svc->urlsafeEncode(chr(4) . $ec['x'] . $ec['y']),
            'private' => $svc->urlsafeEncode($ec['d']),
        ];
    }

    /**
     * Locate a usable openssl.cnf. PHP on Windows fails EC operations when the
     * default config is missing ("system library::No such process").
     */
    public static function opensslConfig(): ?string
    {
        $binary = PHP_BINARY ? dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf' : null;
        $candidates = [
            getenv('OPENSSL_CONF') ?: null,
            $binary,
            'C:/php/php8.4/extras/ssl/openssl.cnf',
            '/etc/ssl/openssl.cnf',
        ];
        foreach ($candidates as $path) {
            if ($path && is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
