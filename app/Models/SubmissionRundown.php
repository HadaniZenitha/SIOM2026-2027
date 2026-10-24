<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionRundown extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'day_date',
        'time_slot',
        'event_name',
        'pic',
        'note',
        'sort_order',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}