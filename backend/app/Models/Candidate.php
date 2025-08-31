<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $table = 'kullanicilar';

    protected $fillable = [
        'ad',
        'soyad',
        'e_posta',
        'sifre',
        'eski_calistigi_yer',
        'telefon',
        'okudugu_egitim',
        'deneyim_duzeyi',
    ];

    protected $hidden = [
        'sifre',
        'api_token',
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'kullanici_id');
    }
}
