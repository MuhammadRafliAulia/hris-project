<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id', 'section_id', 'type', 'question', 'options', 'is_required', 'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function section()
    {
        return $this->belongsTo(SurveySection::class, 'section_id');
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }
}
