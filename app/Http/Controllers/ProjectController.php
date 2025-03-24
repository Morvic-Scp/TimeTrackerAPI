<?php

namespace App\Http\Controllers;

use App\Models\project;
use App\Models\project_task;
use App\Models\User;
use App\Models\user_roles;
use App\Models\user_workgroup;
use App\Models\workgroup_projects;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{

    protected $projectModel;

    private function returnProjectHours($ProjectArray){
        $sampleProject = $ProjectArray->map(function ($projectItem){
            $task = project_task::where('projectid', $projectItem->id)
            ->select('startTimeDate', 'endTimeDate')
            ->get();

            // return ($task);

            $projectItem['project_hours'] = 0;
            foreach($task as $taskTime){
                if(isset($taskTime->startTimeDate) && isset($taskTime->endTimeDate)){
                    $start = Carbon::parse($taskTime->startTimeDate);
                    $end = Carbon::parse($taskTime->endTimeDate);
                    $projectItem['project_hours'] += $start->diffInHours($end) ;
                }
            }

            return $projectItem;

        });

        // dd($ProjectArray);
        return $sampleProject;
    }

    public function __construct(project $project)
    {
        $this->projectModel = $project;
    }

    public function getProjects(Request $request){
        // check if user is admin
        $isAdmin = user_roles::where('userid',auth()->user()->id)->where('roleid',1)->exists();


        $userId = $request->query('userid');

        if ($userId) {
            if($isAdmin){
                $projects = Project::
                with(['tasks','creator:id,name'])
                ->get()->map(function ($project) {
                    $project->created_person_name = $project->creator->name ?? null;
                    unset($project->creator);
                    return $project;
                });

                return response()->json($this->returnProjectHours($projects));
            }else{
                $projects = Project::
            where('created_by', $userId)
            ->orWhereHas('projects', function ($query) use ($userId) {
                $query->whereHas('userWorkgroups', function ($query) use ($userId) {
                    $query->where('userid', $userId);
                });
            })
            ->orWhereHas('directProjects', function ($query) use ($userId) {
                $query->where('userid', $userId);
            })
            ->with(['tasks','creator:id,name'])
            ->get()->map(function ($project) {
                $project->created_person_name = $project->creator->name ?? null;
                unset($project->creator);
                return $project;
            });

            return response()->json($this->returnProjectHours($projects));
            }

        }else{
            if($isAdmin){

                $projects = project::with('creator:id,name')->get()->map(function ($project) {
                $project->created_person_name = $project->creator->name ?? null;
                unset($project->creator);
                return $project;
            });
            return $this->returnProjectHours($projects);
            }else{
                $projects = project::where('created_by',auth()->user()->id)->with('creator:id,name')->get()->map(function ($project) {
                    $project->created_person_name = $project->creator->name ?? null;
                    unset($project->creator);
                    return $project;
                });
                return $this->returnProjectHours($projects);
            }

        }
    }

    public function getUserProjects(Request $request){
        $projectid = $request->query('projectid');
        // dd($request->user()->id);
        if(!$projectid){
            return response()->json([
                 'message' => 'projectid is Required.'
            ],404);
        }

        $userProjects = project::with([
        'projects.users:id,name,email'
        ])
        ->where('id', $projectid)
        ->first();

        if (!$userProjects) {
            return response()->json(['message' => 'No project found'], 404);
        }

        // return response()->json([
        //     $userProjects
        // ],201);

        // Flatten the users list
        $assignedUsers = $userProjects->projects->flatMap->users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ];
        });

        return response()->json([
            'id' => $userProjects->id,
            'name' => $userProjects->name,
            'duration' => $userProjects->duration,
            'status' => $userProjects->status,
            'color' => $userProjects->color,
            'created_person_name' => $userProjects->created_person_name,
            'created_by' => $userProjects->created_by,
            'assigned_users' => $assignedUsers
        ]);



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
            // 'public' => $request->public,
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
        $this->authorize('update',  $project);

        $createProject =  $project->update([
            'name' => $request->name,
            'duration' => $request->duration,
            'color' => $request->color,
            'created_by' => $request->created_by,
            'favorite' => $request->favorite,
            // 'public' => $request->public,
        ]);

        return response()->json([
            'message' => 'Project update successful'
        ], 201);
    }



    public function destroy(Request $request)
    {
        $request->validate([
            'project_ids' => 'array',
            'project_ids.*' => 'exists:projects,id',
            'project_id' => 'exists:projects,id',
        ]);
        $user = auth()->user();
        if($request->project_id){
            $project = Project::where('id', $request->project_id)
                  ->where('created_by', $user->id)
                  ->first();

            if (!$project) {
                return response()->json(['message' => 'Project not found or unauthorized'], 403);
            }

            // Delete the project
            $hasTasks=project_task::where('projectid',$project->id)->exists();
            if(!$hasTasks){
                $project->delete();

                return response()->json([
                    'message' => 'Projects deleted successfully',
                ], 200);
            }else{
                return response()->json([
                    'message' => 'This Project has tasks assigned to it.',
                ], 403);
            }


        }else if(isset($request->project_ids)){

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
        }else{
            return response()->json([
                'message' => "Please specify key as 'project_id' or 'project_ids'"
            ], 404);
        }
    }
}
