<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function members()
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function budgets()
    {
        return $this->hasMany(OrganizationBudget::class);
    }

    public function cashAccounts()
    {
        return $this->hasMany(CashAccount::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function lpjs()
    {
        return $this->hasMany(Lpj::class);
    }
}
