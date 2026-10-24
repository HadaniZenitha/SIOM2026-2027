<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'role_id',
        'sequence',
        'status',
        'approved_by',
        'approved_at',
        'note',
        'signature_path',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
