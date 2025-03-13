<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_roles extends Model
{
    use HasFactory;
    protected $fillable=[
        "userid",
        "roleid"
    ];

    public function userRoles()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function RolesList()
    {
        return $this->belongsTo(roles::class, 'roleid');
    }
}
