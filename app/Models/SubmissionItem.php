<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'unit',
        'item_name',
        'qty',
        'unit',
        'price',
        'total_price',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
