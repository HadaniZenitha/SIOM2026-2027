<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    protected $fillable = [
        'period_id',
        'ormawa_id',
        'created_by',
        'title',
        'meeting_date',
        'start_time',
        'end_time',
        'location',
        'minutes_type',
        'minutes_text',
        'minutes_file',
    ];

    protected function casts(): array
    {
        return ['meeting_date' => 'date'];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function ormawa(): BelongsTo
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(MeetingAttendance::class);
    }
}
