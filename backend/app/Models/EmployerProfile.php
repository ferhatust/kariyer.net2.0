<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerProfile extends Model
{
    use HasFactory;

    protected $table = 'isveren_profilleri';

    protected $fillable = [
        'isveren_id',
        'sirket_adi',
        'konum',
        'hakkinda',
    ];

    public function isveren()
    {
        return $this->belongsTo(Employer::class, 'isveren_id');
    }
}
