<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'approval_step_id',
        'user_id',
        'action',
        'note',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function approvalStep()
    {
        return $this->belongsTo(ApprovalStep::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
