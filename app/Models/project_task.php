<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class project_task extends Model
{
    use HasFactory;
    protected $table = "project_task";
    protected $fillable=[
        "projectid",
        "created_by",
        "workgroupid",
        "startTimeDate",
        "endTimeDate",
        "description",
    ];

    public function project()
    {
        return $this->belongsTo(project::class, 'projectid');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
