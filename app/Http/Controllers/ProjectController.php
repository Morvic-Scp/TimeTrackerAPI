<?php

namespace App\Http\Controllers;

use App\Models\project;
use App\Models\User;
use App\Models\user_workgroup;
use App\Models\workgroup_projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{

    protected $projectModel;
    public function __construct(project $project)
    {
        $this->projectModel = $project;
    }
    public function getProjects(Request $request){
        $userId = $request->query('userid');
        if ($userId) {
            $projects = Project::
            // where('created_by', $userId)
            orWhereHas('projects', function ($query) use ($userId) {
                $query->whereHas('userWorkgroups', function ($query) use ($userId) {
                    $query->where('userid', $userId);
                });
            })
            // ->orWhereHas('directProjects', function ($query) use ($userId) {
            //     $query->where('userid', $userId);
            // })
            ->with('tasks')
            ->get();

            return response()->json($projects);
        }else{
            $projects = project::all();
            return $projects;
        }
    }

    public function createProject(Request $request){
        $request->validate([
            "name"=>'required',
            "duration"=>'required',
            "color"=>'required',
            "created_by"=>'required|exists:users,id',
            "assignees_User_Ids"=>'array',
            "assignees_User_Ids.*"=>'exists:users,id',
            "workgroup_ids"=>'array',
            "workgroup_ids.*"=>'exists:workgroups,id'
        ]);

        $createProject = project::create([
            'name' => $request->name,
            'duration' => $request->duration,
            'color' => $request->color,
            'public' => $request->public,
            'created_by' => $request->created_by,
        ]);

        if(!empty($request->assignees_User_Ids) || !empty($request->workgroup_ids)){
            foreach ($request->workgroup_ids as $workgroupId) {
                $searchWorkgroups = workgroup_projects::where('workgroupid',$workgroupId)->exists();
                if(!$searchWorkgroups){
                    $assignPersonnel = workgroup_projects::create([
                        "projectid"=>$createProject->id,
                        "workgroupid"=>$workgroupId,
                        // "userid"=>
                    ]);

                }
            }

            foreach ($request->assignees_User_Ids as $assigneeID) {
                $searchusersInProject = user_workgroup::where('userid',$assigneeID)->exists();
                $searchusersInGroup = workgroup_projects::where('userid',$assigneeID)->exists();
                if(!$searchusersInGroup && !$searchusersInProject){
                    $assignUser = workgroup_projects::create([
                        "projectid"=>$createProject->id,
                        // "workgroupid"=>$workgroupId,
                        "userid"=>$assigneeID
                    ]);

                }
            }
        }

        return response()->json([
            'message' => 'Project creation successful'
        ], 201);
    }


    public function updateProject(Request $request,project $project){
        $request->validate([
            "name"=>'required',
            "duration"=>'required',
            "color"=>'required',
            "created_by"=>'required|exists:users,id',
        ]);
        // dd(auth()->user());
        $this->authorize('update',  $project);

        $createProject =  $project->update([
            'name' => $request->name,
            'duration' => $request->duration,
            'color' => $request->color,
            'created_by' => $request->created_by,
            'favorite' => $request->favorite,
            'public' => $request->public,
        ]);

        return response()->json([
            'message' => 'Project update successful'
        ], 201);
    }



    public function destroy(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
        ]);
        $user = auth()->user();

        // Find projects that belong to the authenticated user
        $projects = project::whereIn('id', $request->project_ids)
                            ->where('created_by', $user->id)
                            ->get();

        if ($projects->isEmpty()) {
            return response()->json(['message' => 'No projects found or unauthorized'], 403);
        }

        // Delete the projects
        project::whereIn('id', $projects->pluck('id'))->delete();

        return response()->json([
            'message' => 'Projects deleted successfully',
            'deleted_ids' => $projects->pluck('id')
        ], 200);
    }
}
