<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'ormawa_id',
        'budget_amount',
        'allocated_amount',
        'remaining_amount',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class);
    }
}
