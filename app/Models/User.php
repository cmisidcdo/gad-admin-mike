<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $table = 'users';

    protected $fillable = [
        // 'name',
        // 'email',
        // 'password',
        'user_id',
        'name',
        'email',
        'user_role_id',
        'office',
        'is_active',
        'password',
    ];

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
        'password' => 'hashed',
    ];

    public function department()
    {
        return $this->belongsTo(TblRefDepartment::class, 'office', 'department_id');
    }

    public function role()
    {
        return $this->belongsTo(TblUserRole::class, 'user_role_id', 'user_role_id');
    }

    public function sectorPermissions()
    {
        return $this->hasMany(TblUserSectorPermission::class, 'id_user', 'id');
    }

    public function search($query, $value)
    {
        return $query->with('department')
            ->where(function ($q) use ($value) {
                $q->where('name', 'like', '%' . $value . '%')
                    ->orWhere('user_id', 'like', '%' . $value . '%')
                    ->orWhere('office', 'like', '%' . $value . '%');
            });
    }
}

