<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpjDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'lpj_id',
        'type',
        'file_path',
        'file_name',
    ];

    public function lpj()
    {
        return $this->belongsTo(Lpj::class);
    }
}
