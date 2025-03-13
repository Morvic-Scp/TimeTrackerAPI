<?php

namespace App\Http\Controllers;

use App\Models\roles;
use App\Models\workgroups;
use Illuminate\Http\Request;

class APISummaryController extends Controller
{
    public function allWorkgroups(Request $request)
    {
        $usersList = workgroups::all();
        return response()->json($usersList);
    }


    public function allRoles(Request $request)
    {
        $allRoles = roles::all();
        return response()->json($allRoles);
    }


    public function CreateWorkgroups(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:workgroups,name',
            'description' => 'required'
        ]);


         // Create the workgroup
         $workgroup = workgroups::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => "Active",
        ]);


        // Return response
        return response()->json([
            'message' => 'Workgroup Created successfully',
            'workgroup_id' => $workgroup->id,
        ], 201);
    }



    public function CreateRoles(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,role_name',
        ]);


         // Create the workgroup
         $role = roles::create([
            'role_name' => $request->name,
            'status' => "Active",
        ]);


        // Return response
        return response()->json([
            'message' => 'Role Created successfully',
            'role_id' => $role->id,
        ], 201);
    }
}
