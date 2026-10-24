<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpjApprovalLog extends Model
{
    protected $fillable = ['lpj_id', 'lpj_approval_step_id', 'user_id', 'action', 'note'];

    public function lpj(): BelongsTo
    {
        return $this->belongsTo(Lpj::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LpjApprovalStep::class, 'lpj_approval_step_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
