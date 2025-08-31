<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Candidate;

class BasvuruSeeder extends Seeder
{
    public function run(): void
    {
        $ilan = JobPost::first();
        $kullanici = Candidate::first();
        if (!$ilan || !$kullanici) return;

        JobApplication::create([
            'is_ilani_id' => $ilan->id,
            'kullanici_id' => $kullanici->id,
            'on_yazi' => 'Pozisyona uygunum, değerlendirilmek isterim.',
            'durum' => 'basvuruldu',
        ]);
    }
}
