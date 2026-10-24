<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationDivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'ormawa_id',
        'name',
    ];

    public function ormawa(): BelongsTo
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }
}
