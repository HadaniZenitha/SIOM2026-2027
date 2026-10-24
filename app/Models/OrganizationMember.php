<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'ormawa_id',
        'program_studi_id',
        'organization_division_id',
        'position',
        'position_number',
        'name',
        'birth_place',
        'birth_date',
        'nim',
        'phone',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'position_number' => 'integer',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function ormawa(): BelongsTo
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(OrganizationDivision::class, 'organization_division_id');
    }
}
