<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTest extends Model
{
    use HasFactory;

    protected $fillable = ['bank_id', 'title', 'description', 'order', 'duration_minutes', 'type', 'kraepelin_config', 'disc_config', 'papikostik_config'];

    protected $casts = [
        'kraepelin_config' => 'array',
        'disc_config' => 'array',
        'papikostik_config' => 'array',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->where('is_example', false)->orderBy('order');
    }

    public function exampleQuestions()
    {
        return $this->hasMany(Question::class)->where('is_example', true)->orderBy('order');
    }

    public function allQuestions()
    {
        return $this->hasMany(Question::class)->orderBy('is_example', 'desc')->orderBy('order');
    }
}
