<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class workgroups extends Model
{
    use HasFactory;
    protected $fillable =[
        'name',
        'description',
        'status'
    ];

    public function workgroupTies()
    {
        return $this->hasMany(user_workgroup::class, 'workgroupid');
    }

    public function userWorkgroups()
    {
        return $this->hasMany(user_workgroup::class, 'workgroupid');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_workgroups', 'workgroupid', 'userid');
    }
}
