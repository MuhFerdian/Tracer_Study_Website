<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class userModel extends Authenticatable
{
    use HasFactory;
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
    ];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'password' => 'hashed',
        'tanggal_lahir' => 'date'
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(roleModel::class, 'role_id', 'role_id');
    }
    
    public function alumni(): HasMany
    {
        return $this->hasMany(alumniModel::class, 'user_id', 'id');
    }
}
