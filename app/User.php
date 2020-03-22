<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    public $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'department', 'position', 'barangay_id', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'role_id', 'barangay_id', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function barangay() {
        return $this->belongsTo(Barangay::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    /** 
     * @param string|array $roles
     */
    public function authorizeRoles($roles) {
        if ($this->hasRole($roles)) {
            return true;
        }
        return false;
    }
    
    /**
     * Check one role
     * 
     * @param string $role
     */
    public function hasRole($role) {
        if ($this->where('role_id', $role)->first()) {
            return true;
        }
        return false;
    }

    /**
     * Check if user is authorized for the request
     * 
     * @param int $user_id
     */
    public function verifyUser($user_id) {
        if ($this->id == $user_id) {
            return true;
        }
        return false;
    }
}
