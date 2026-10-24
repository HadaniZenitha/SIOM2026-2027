<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'stage',
        'start_date',
        'end_date',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'description' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}