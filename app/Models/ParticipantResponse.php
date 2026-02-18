<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantResponse extends Model
{
    use HasFactory;

    protected $fillable = ['bank_id', 'nik', 'participant_name', 'participant_email', 'phone', 'address', 'position', 'department', 'applicant_username', 'applicant_password', 'token', 'responses', 'score', 'completed', 'started_at', 'completed_at', 'violation_count', 'violation_log', 'anti_cheat_note'];

    protected $casts = [
        'responses' => 'array',
        'violation_log' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
