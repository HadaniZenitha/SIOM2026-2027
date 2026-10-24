<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpjItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lpj_id',
        'submission_item_id',
        'item_name',
        'qty',
        'unit',
        'price',
        'total_price',
        'sort_order',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function lpj()
    {
        return $this->belongsTo(Lpj::class);
    }

    public function submissionItem()
    {
        return $this->belongsTo(SubmissionItem::class);
    }
}
