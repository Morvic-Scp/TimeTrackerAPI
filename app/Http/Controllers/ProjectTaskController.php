<?php

namespace App\Http\Controllers;

use App\Models\project_task;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function createProjectTask(Request $request){
        $request->validate([
            "projectid"=>'required|exists:projects,id',
            "created_by"=>'required|exists:users,id',
            "workgroupid"=>'required|exists:workgroups,id',
            "startTimeDate"=>'required',
            // "endTimeDate"=>'required',
            "description"=>'required',
        ]);

        $createProject = project_task::create([
            "projectid"=> $request->projectid,
            "created_by"=> $request->created_by,
            "workgroupid"=> $request->workgroupid,
            "startTimeDate"=> $request->startTimeDate,
            // "endTimeDate"=> $request->endTimeDate,
            "project_date"=> $request->project_date,
            "billable"=> $request->billable,
            "description"=> $request->description,
        ]);

        return response()->json([
            'message' => 'Project task creation successful'
        ], 201);
    }


    public function updateProjectTask(Request $request,project_task $task){
        $request->validate([
          "projectid"=>'required|exists:projects,id',
            "created_by"=>'required|exists:users,id',
            "workgroupid"=>'required|exists:workgroups,id',
            "startTimeDate"=>'required',
            "endTimeDate"=>'required',
            "description"=>'required',
        ]);

        $this->authorize('update',  $task);

        $updateProject =  $task->update([
          "projectid"=> $request->projectid,
            "created_by"=> $request->created_by,
            "workgroupid"=> $request->workgroupid,
            // "startTimeDate"=> $request->startTimeDate,
            "endTimeDate"=> $request->endTimeDate,
            "project_date"=> $request->project_date,
            "billable"=> $request->billable,
            "description"=> $request->description,
        ]);

        return response()->json([
            'message' => 'Project task update successful'
        ], 201);
    }



    public function destroy(Request $request)
    {
        $request->validate([
            'project_task_ids' => 'required|array',
            'project_task_ids.*' => 'exists:projects,id',
        ]);
        $user = auth()->user();

        // Find projects that belong to the authenticated user
        $projects = project_task::whereIn('id', $request->project_task_ids)
                            ->where('created_by', $user->id)
                            ->get();

        if ($projects->isEmpty()) {
            return response()->json(['message' => 'No projects task found or unauthorized'], 403);
        }

        // Delete the projects
        project_task::whereIn('id', $projects->pluck('id'))->delete();

        return response()->json([
            'message' => 'Projects task deleted successfully',
            'deleted_ids' => $projects->pluck('id')
        ], 200);
    }
}
