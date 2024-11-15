<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profile';
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'username',
        'first_name',
        'last_name',
        'biography',
        'birth_date',
        'country_code',
        'avatar',
    ];

    protected $casts = [
        'birth_date' => 'datetime',
    ];

    protected $hidden = ['biography', 'birth_date'];

    public function getFullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    public function isActive()
    {
        return User::where('id', $this->user_id)->where('active', true)->exists();
    }
}
