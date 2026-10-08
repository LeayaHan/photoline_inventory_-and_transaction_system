<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'position',
        'date_hired',
        'emergency_contact_name',
        'emergency_contact_phone',
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
            'date_hired' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}
