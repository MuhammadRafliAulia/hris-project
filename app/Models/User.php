<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property string $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bank[] $banks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Employee[] $employees
 * @method bool isSuperAdmin()
 * @method bool isAdminProd()
 * @method bool isRecruitmentTeam()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdminProd()
    {
        return $this->role === 'admin_prod';
    }

    public function isRecruitmentTeam()
    {
        return $this->role === 'recruitmentteam';
    }

    public function isTopLevelManagement()
    {
        return $this->role === 'top_level_management';
    }

    public function isInternalHR()
    {
        return $this->role === 'internal_hr';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
