<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Survey extends Model
{
    protected $fillable = [
        'title', 'description', 'slug', 'token', 'status',
        'is_anonymous', 'start_date', 'end_date', 'created_by',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($survey) {
            if (empty($survey->slug)) {
                $survey->slug = Str::slug($survey->title) . '-' . Str::random(6);
            }
            if (empty($survey->token)) {
                $survey->token = Str::random(48);
            }
        });
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') return false;
        $today = now()->startOfDay();
        if ($this->start_date && $today->lt($this->start_date->startOfDay())) return false;
        if ($this->end_date && $today->gt($this->end_date->endOfDay())) return false;
        return true;
    }

    public function getPublicUrlAttribute(): string
    {
        return url('hrissdi/survey/' . $this->token . '/fill');
    }
}
