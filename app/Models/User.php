<?php

namespace App\Models;

use App\Models\Common\File;

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
    protected $table = 'Users';
    // protected $fillable = [
    //     'name',
    //     'username',
    //     'email',
    //     'password',
    //     'department_id',
    //     'status_id',
    //     'remember_token'
    // ];
    protected $guarded = [];

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

    public function image(){
        return $this->morphOne(File::class,'model')->where('custom_field','Avatar');

    }

    public function canDelete()
    {
        return true;
    }

    public function orders(){
        return $this->hasMany(orders::class);
    }

}
