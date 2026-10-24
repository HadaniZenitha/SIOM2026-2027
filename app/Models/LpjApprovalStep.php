<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpjApprovalStep extends Model
{
    protected $fillable = [
        'lpj_id',
        'role_id',
        'sequence',
        'status',
        'approved_by',
        'approved_at',
        'note',
        'signature_path',
    ];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function lpj(): BelongsTo
    {
        return $this->belongsTo(Lpj::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
