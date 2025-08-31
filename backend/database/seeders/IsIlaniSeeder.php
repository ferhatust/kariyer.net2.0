<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPost;
use App\Models\Employer;

class IsIlaniSeeder extends Seeder
{
    public function run(): void
    {
        $isveren = Employer::first();
        if (!$isveren) return;

        JobPost::create([
            'isveren_id' => $isveren->id,
            'sirket_adi' => $isveren->sirket_adi,
            'baslik' => 'Full Stack Geliştirici',
            'aciklama' => 'Laravel + Vue deneyimli geliştirici arıyoruz.',
            'konum' => 'İstanbul / Hibrit',
            'uzaktan_mi' => true,
            'maas' => 45000,
            'para_birimi' => 'TRY',
            'deneyim_duzeyi' => 'Orta',
            'son_tarih' => now()->addMonth(),
        ]);
    }
}
