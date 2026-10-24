<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lpj extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'period_id',
        'ormawa_id',
        'created_by',
        'title',
        'description',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->hasMany(LpjDocument::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LpjItem::class)->orderBy('sort_order');
    }

    public function approvalSteps()
    {
        return $this->hasMany(LpjApprovalStep::class)->orderBy('sequence');
    }

    public function approvalLogs()
    {
        return $this->hasMany(LpjApprovalLog::class)->orderBy('created_at');
    }
}
