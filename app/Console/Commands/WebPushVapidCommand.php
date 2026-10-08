<?php

namespace App\Console\Commands;

use App\Services\WebPushService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('webpush:vapid {--write : Append the keys to the .env file}')]
#[Description('Generate a VAPID key pair for Web Push (RFC 8292)')]
class WebPushVapidCommand extends Command
{
    public function handle(): int
    {
        $keys = WebPushService::generateVapidKeys();

        $this->info('VAPID key pair generated.');
        $this->line('VAPID_PUBLIC_KEY=' . $keys['public']);
        $this->line('VAPID_PRIVATE_KEY=' . $keys['private']);

        if ($this->option('write')) {
            $env = base_path('.env');
            $content = file_get_contents($env);
            $lines = "VAPID_PUBLIC_KEY={$keys['public']}\nVAPID_PRIVATE_KEY={$keys['private']}\n";

            // Replace existing keys if present, otherwise append.
            if (preg_match('/^VAPID_PUBLIC_KEY=/m', $content)) {
                $content = preg_replace('/^VAPID_PUBLIC_KEY=.*$/m', 'VAPID_PUBLIC_KEY=' . $keys['public'], $content);
                $content = preg_replace('/^VAPID_PRIVATE_KEY=.*$/m', 'VAPID_PRIVATE_KEY=' . $keys['private'], $content);
            } else {
                $content = rtrim($content) . "\n\n" . $lines;
            }

            file_put_contents($env, $content);
            $this->info('Keys written to .env');
        }

        return self::SUCCESS;
    }
}
