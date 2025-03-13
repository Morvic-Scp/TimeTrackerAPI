<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class project extends Model
{
    use HasFactory;
    protected $table="projects";
    protected $fillable = [
        "name",
        "duration",
        "public",
        "favorite",
        "color",
        "created_by"
    ];

    public function tasks()
    {
        return $this->hasMany(project_task::class, 'projectid');
    }
    public function projects()
    {
        return $this->belongsToMany(workgroups::class, 'workgroup_projects', 'projectid', 'workgroupid');
    }
    public function Directprojects()
    {
        return $this->belongsToMany(project::class, 'workgroup_projects', 'userid', 'projectid');
    }
}
