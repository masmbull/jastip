<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies the pure-PHP Web Push crypto against the RFC 8291 Appendix A
 * test vector, plus VAPID JWT signing and the subscription endpoints.
 */
class WebPushTest extends TestCase
{
    use RefreshDatabase;

    private const AS_PUBLIC = 'BP4z9KsN6nGRTbVYI_c7VJSPQTBtkgcy27mlmlMoZIIgDll6e3vCYLocInmYWAmS6TlzAC8wEqKK6PBru3jl7A8';
    private const AS_PRIVATE = 'yfWPiYE-n46HLnH0KqZOF1fJJU3MYrct3AELtAQ-oRw';
    private const UA_PUBLIC = 'BCVxsr7N_eNgVRqvHtD0zTZsEc6-VV-JvLexhqUzORcxaOzi6-AYWXvTBHm4bjyPjs7Vd8pZGH6SRpkNtoIAiw4';
    private const AUTH = 'BTBZMqHH6r4Tts7J_aSIgg';
    private const SALT = 'DGv6ra1nlYgDCS1FRnbzlw';
    private const PLAINTEXT = 'V2hlbiBJIGdyb3cgdXAsIEkgd2FudCB0byBiZSBhIHdhdGVybWVsb24';
    private const EXP_ECDH = 'kyrL1jIIOHEzg3sM2ZWRHDRB62YACZhhSlknJ672kSs';
    private const EXP_PRK_KEY = 'Snr3JMxaHVDXHWJn5wdC52WjpCtd2EIEGBykDcZW32k';
    private const EXP_IKM = 'S4lYMb_L0FxCeq0WhDx813KgSYqU26kOyzWUdsXYyrg';
    private const EXP_CEK = 'oIhVW04MRdy2XN9CiKLxTg';
    private const EXP_NONCE = '4h_95klXJ5E_qnoN';
    private const EXP_CIPHER = '8pfeW0KbunFT06SuDKoJH9Ql87S1QUrdirN6GcG7sFz1y1sqLgVi1VhjVkHsUoEsbI_0LpXMuGvnzQ';

    private function service(): WebPushService
    {
        return new WebPushService([
            'public_key' => self::AS_PUBLIC,
            'private_key' => self::AS_PRIVATE,
        ]);
    }

    private function invoke(object $obj, string $method, array $args = []): mixed
    {
        $ref = new \ReflectionMethod($obj, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($obj, $args);
    }

    public function test_encryption_matches_rfc8291_appendix_a(): void
    {
        $svc = $this->service();

        [$asPub, $privObj] = $this->invoke($svc, 'serverKeys');
        $this->assertSame(self::AS_PUBLIC, $svc->urlsafeEncode($asPub));

        $uaPub = $svc->urlsafeDecode(self::UA_PUBLIC);
        $auth = $svc->urlsafeDecode(self::AUTH);
        $salt = $svc->urlsafeDecode(self::SALT);

        // ECDH shared secret (validates SEC1/SPKI key reconstruction + derive)
        $ecdh = $this->invoke($svc, 'ecdh', [$privObj, $uaPub]);
        $this->assertSame(self::EXP_ECDH, $svc->urlsafeEncode($ecdh));

        // HKDF: PRK_key -> IKM
        $prkKey = $this->invoke($svc, 'hkdfExtract', [$auth, $ecdh]);
        $this->assertSame(self::EXP_PRK_KEY, $svc->urlsafeEncode($prkKey));

        $ikm = $this->invoke($svc, 'hkdfExpand', [$prkKey, "WebPush: info\x00" . $uaPub . $asPub, 32]);
        $this->assertSame(self::EXP_IKM, $svc->urlsafeEncode($ikm));

        // CEK + nonce
        $prk = $this->invoke($svc, 'hkdfExtract', [$salt, $ikm]);
        $cek = $this->invoke($svc, 'hkdfExpand', [$prk, "Content-Encoding: aes128gcm\x00", 16]);
        $nonce = $this->invoke($svc, 'hkdfExpand', [$prk, "Content-Encoding: nonce\x00", 12]);
        $this->assertSame(self::EXP_CEK, $svc->urlsafeEncode($cek));
        $this->assertSame(self::EXP_NONCE, $svc->urlsafeEncode($nonce));

        // AES-128-GCM over the 0x02-padded plaintext
        $tag = '';
        $cipher = openssl_encrypt(
            $svc->urlsafeDecode(self::PLAINTEXT) . "\x02",
            'aes-128-gcm',
            $cek,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            '',
            16
        );
        $this->assertSame(self::EXP_CIPHER, $svc->urlsafeEncode($cipher . $tag));
    }

    public function test_generated_vapid_keys_sign_verifiable_es256_jwt(): void
    {
        $keys = WebPushService::generateVapidKeys();
        $tmp = WebPushService::fromConfig();
        $this->assertSame(65, strlen($tmp->urlsafeDecode($keys['public'])));
        $this->assertSame(32, strlen($tmp->urlsafeDecode($keys['private'])));

        $svc = new WebPushService([
            'public_key' => $keys['public'],
            'private_key' => $keys['private'],
            'subject' => 'mailto:test@example.com',
        ]);

        $jwt = $this->invoke($svc, 'vapidToken', ['https://push.example.com']);
        [$h, $p, $sig] = explode('.', $jwt);
        $this->assertSame('https://push.example.com', json_decode($svc->urlsafeDecode($p), true)['aud']);

        // Verify the ES256 signature (raw r||s -> DER) with the VAPID public key.
        $raw = $svc->urlsafeDecode($sig);
        $this->assertSame(64, strlen($raw));
        $pubRaw = $svc->urlsafeDecode($keys['public']);
        $pubPem = $this->invoke($svc, 'spkiPem', [substr($pubRaw, 1, 32), substr($pubRaw, 33, 32)]);
        $ok = openssl_verify($h . '.' . $p, $this->rawToDer($raw), openssl_pkey_get_public($pubPem), OPENSSL_ALGO_SHA256);
        $this->assertSame(1, $ok);
    }

    public function test_push_endpoints_register_and_remove_subscription(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('push.subscribe'), [
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc123',
                'keys' => ['p256dh' => self::UA_PUBLIC, 'auth' => self::AUTH],
                'contentEncoding' => 'aes128gcm',
            ])
            ->assertOk();

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc123',
        ]);

        $this->actingAs($user)
            ->postJson(route('push.unsubscribe'), ['endpoint' => 'https://fcm.googleapis.com/fcm/send/abc123'])
            ->assertOk();

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    private function rawToDer(string $raw): string
    {
        $encode = function (string $v): string {
            $v = ltrim($v, "\x00");
            if ($v === '' || (ord($v[0]) & 0x80)) {
                $v = "\x00" . $v;
            }

            return "\x02" . chr(strlen($v)) . $v;
        };
        $seq = $encode(substr($raw, 0, 32)) . $encode(substr($raw, 32, 32));

        return "\x30" . chr(strlen($seq)) . $seq;
    }
}
