<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'slug', 'is_active', 'duration_minutes', 'target'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($bank) {
            if (!$bank->slug) {
                $bank->slug = Str::slug($bank->title) . '-' . uniqid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function subTests()
    {
        return $this->hasMany(SubTest::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(ParticipantResponse::class);
    }

    public function applicantCredentials()
    {
        return $this->hasMany(\App\Models\ApplicantCredential::class)->orderBy('created_at', 'desc');
    }
}
