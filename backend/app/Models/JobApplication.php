<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $table = 'basvurular';

    protected $fillable = [
        'is_ilani_id',
        'kullanici_id',
        'on_yazi',
        'durum',
    ];

    public function isIlani()
    {
        return $this->belongsTo(JobPost::class, 'is_ilani_id');
    }

    public function kullanici()
    {
        return $this->belongsTo(Candidate::class, 'kullanici_id');
    }
}
