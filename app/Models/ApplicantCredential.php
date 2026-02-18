<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantCredential extends Model
{
    use HasFactory;

    protected $fillable = ['bank_id', 'username', 'password_encrypted', 'used'];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
