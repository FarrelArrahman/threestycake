<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create(
            [
                'type' => 'account_name',
                'value' => 'Nama Pemilik Rekening',
            ],
        );

        Setting::create(
            [
                'type' => 'account_number',
                'value' => 'Nomor Rekening',
            ],
        );

        Setting::create(
            [
                'type' => 'bank_name',
                'value' => 'Nama Bank',
            ],
        );

        Setting::create(
            [
                'type' => 'address',
                'value' => 'Alamat',
            ],
        );

        Setting::create(
            [
                'type' => 'phone',
                'value' => 'Nomor HP',
            ],
        );

        Setting::create(
            [
                'type' => 'instagram',
                'value' => 'Username instagram',
            ],
        );
    }
}
