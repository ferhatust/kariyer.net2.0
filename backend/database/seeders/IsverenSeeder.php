<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employer;
use Illuminate\Support\Facades\Hash;

class IsverenSeeder extends Seeder
{
    public function run(): void
    {
        Employer::create([
            'sirket_adi' => 'Kariyer Dünyam A.Ş.',
            'e_posta' => 'hr@kariyerdunyam.com',
            'sifre' => Hash::make('secret123'),
            'konum' => 'İstanbul',
            'telefon' => '+90 555 000 0000',
            'hakkinda' => 'Harika bir ekip.',
        ]);
    }
}
