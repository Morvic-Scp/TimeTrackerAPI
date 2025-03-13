<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class roles extends Model
{
    use HasFactory;
    protected $fillable=[
        "role_name",
        "status"
    ];
    protected $hidden = ['pivot'];

    public function userRoles()
    {
        return $this->hasMany(user_roles::class, 'roleid');
    }
}
