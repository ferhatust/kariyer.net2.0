<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    protected $table = 'is_ilanlari';

    protected $fillable = [
        'isveren_id',
        'sirket_adi',
        'baslik',
        'aciklama',
        'konum',
        'uzaktan_mi',
        'maas',
        'para_birimi',
        'deneyim_duzeyi',
        'son_tarih',
    ];

    protected $casts = [
        'uzaktan_mi' => 'boolean',
        'son_tarih' => 'date',
        'maas' => 'decimal:2',
    ];

    public function isveren()
    {
        return $this->belongsTo(Employer::class, 'isveren_id');
    }

    public function basvurular()
    {
        return $this->hasMany(JobApplication::class, 'is_ilani_id');
    }
}
