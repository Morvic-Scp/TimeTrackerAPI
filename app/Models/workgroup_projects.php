<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class workgroup_projects extends Model
{
    use HasFactory;

    protected $fillable=[
        "projectid",
        "workgroupid",
        "userid"
    ];
}
