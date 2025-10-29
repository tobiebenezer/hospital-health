<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site_name', 'value' => 'HospitalApp'],
            ['key' => 'currency', 'value' => 'USD'],
            ['key' => 'support_email', 'value' => 'support@example.com'],
            ['key' => 'timezone', 'value' => 'UTC'],
        ];

        foreach ($defaults as $item) {
            SystemSetting::updateOrCreate(['key' => $item['key']], ['value' => $item['value']]);
        }
    }
}
