<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;  // Uncomment if you want email verification

class User extends Authenticatable implements MustVerifyEmail  // Implement MustVerifyEmail if needed
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // Ensure password is always hashed when retrieved
    ];

    // Optionally, if you want to add relationships (example for tasks)
    // public function tasks()
    // {
    //     return $this->hasMany(Task::class, 'assigned_to'); // Assuming 'assigned_to' is a user ID in tasks
    // }
}
