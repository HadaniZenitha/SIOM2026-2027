<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionCommittee extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'name',
        'structural_role',
        'committee_role',
        'description',
        'sort_order',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}