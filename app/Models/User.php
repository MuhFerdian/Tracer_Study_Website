<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens; // WAJIB

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id',
        'username',
        'name',
        'email',
        'nim',
        'status',
        'tanggal_lahir',
        'password',
        'fcm_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
        'tanggal_lahir' => 'date'
    ];

    // =========================
    // RELASI ROLE
    // =========================
    public function role(): BelongsTo
    {
        return $this->belongsTo(roleModel::class, 'role_id', 'role_id');
    }

    // =========================
    // RELASI ALUMNI (FIX)
    // =========================
    public function alumni(): HasOne
    {
        return $this->hasOne(alumniModel::class, 'user_id', 'id');
    }
}