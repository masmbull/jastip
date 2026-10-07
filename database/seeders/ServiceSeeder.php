<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Database',
                'description' => 'Main MySQL database connection',
                'status' => 'online',
            ],
            [
                'name' => 'Cache',
                'description' => 'Redis/File cache system',
                'status' => 'online',
            ],
            [
                'name' => 'API',
                'description' => 'REST API endpoints',
                'status' => 'online',
            ],
            [
                'name' => 'Email',
                'description' => 'Email service (SMTP)',
                'status' => 'online',
            ],
            [
                'name' => 'Storage',
                'description' => 'File storage system',
                'status' => 'online',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
