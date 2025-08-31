<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;

    protected $table = 'isverenler';

    protected $fillable = [
        'sirket_adi',
        'e_posta',
        'sifre',
        'konum',
        'sektor',
        'website',
        'telefon',
        'hakkinda',
    ];

    protected $hidden = [
        'sifre',
        'api_token',
    ];

    public function profil()
    {
        return $this->hasOne(EmployerProfile::class, 'isveren_id');
    }

    public function isIlanlari()
    {
        return $this->hasMany(JobPost::class, 'isveren_id');
    }
}
