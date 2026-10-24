<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'ormawa_id',
        'carry_over_amount',
        'opening_balance',
        'current_balance',
    ];

    protected $casts = [
        'carry_over_amount' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }
}
