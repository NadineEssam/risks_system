<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'domain_username',
        'email',
        'password',
        'sector_id',
        'department_id',
        'job_title',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id', 'sec_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'dep_id');
    }
}