<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'ormawa_id',
        'submitted_by',
        'submission_number',
        'nota_number',

        'activity_name',
        'activity_theme',
        'divisi_name',

        'activity_date',
        'start_time',
        'end_time',
        'location',
        'description',

        'meeting_date',
        'circular_letter',
        'budget_code',

        'background',
        'objectives',
        'benefits',
        'target_participants',

        'event_day',
        'event_date',
        'event_start_time',
        'event_end_time',
        'event_location',

        'chairman_name',
        'chairman_nim',
        'pembina_name',
        'pembina_nuptk',
        'kaprodi_name',
        'kaprodi_nuptk',

        'purpose',
        'status',
        'total_amount',
        'submitted_at',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'meeting_date' => 'date',
        'event_date' => 'date',

        'objectives' => 'array',
        'benefits' => 'array',

        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',

        'event_start_time' => 'datetime:H:i',
        'event_end_time' => 'datetime:H:i',

        'total_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubmissionItem::class);
    }

    public function approvalSteps()
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('sequence');
    }

    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function lpj()
    {
        return $this->hasOne(Lpj::class);
    }

    public function timelines()
    {
        return $this->hasMany(SubmissionTimeline::class)
            ->orderBy('sort_order');
    }

    public function committees()
    {
        return $this->hasMany(SubmissionCommittee::class)
            ->orderBy('sort_order');
    }

    public function rundowns()
    {
        return $this->hasMany(SubmissionRundown::class)
            ->orderBy('sort_order');
    }
}
