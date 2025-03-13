<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_workgroup extends Model
{
    use HasFactory;
    protected $fillable=[
        "userid",
        "workgroupid",
    ];

    public function userTies()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function workgroupTies()
    {
        return $this->belongsTo(workgroups::class, 'workgroupid');
    }

    public function usersMatch()
    {
        return $this->belongsTo(workgroups::class, 'workgroupid');
    }
}
