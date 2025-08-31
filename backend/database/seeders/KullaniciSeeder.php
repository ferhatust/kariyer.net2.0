<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use Illuminate\Support\Facades\Hash;

class KullaniciSeeder extends Seeder
{
    public function run(): void
    {
        Candidate::create([
            'ad' => 'Ali',
            'soyad' => 'Veli',
            'e_posta' => 'ali@example.com',
            'sifre' => Hash::make('secret123'),
            'telefon' => '+90 555 111 1111',
            'okudugu_egitim' => 'Bilgisayar Mühendisliği',
            'eski_calistigi_yer' => 'Örnek Şirket',
            'deneyim_duzeyi' => 'Orta',
        ]);
    }
}
