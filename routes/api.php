<?php

use App\Events\BroadCastNotifications;
use App\Http\Controllers\APISummaryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BroadCastNotificationsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register',[AuthController::class, 'signUp']);
Route::post('/token',[AuthController::class, 'login']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
Route::get('/auth-user', [AuthController::class, 'user']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/current-user',[AuthController::class, 'user']);
    Route::post('/logout',[AuthController::class, 'logout']);
    Route::get('/user-list', [AuthController::class, 'allUsers']);

    Route::get('/user-projects', [ProjectController::class, 'getProjects']);
    Route::post('/create-projects', [ProjectController::class, 'createProject']);
    Route::put('/update-projects/{project}', [ProjectController::class, 'updateProject']);
    Route::delete('/delete-projects', [ProjectController::class, 'destroy']);
    Route::get('/project', [ProjectController::class, 'getUserProjects']);


    Route::post('/create-task', [ProjectTaskController::class, 'createProjectTask']);
    Route::put('/update-task/{task}', [ProjectTaskController::class, 'updateProjectTask']);
    Route::delete('/delete-task', [ProjectTaskController::class, 'destroy']);


    // Send Broadcast Messages
    Route::post('/send-notification', [BroadCastNotificationsController::class,'sendMessage']);


    // API Collections
    Route::get('/workgroup-list', [APISummaryController::class, 'allWorkgroups']);
    Route::get('/user-roles', [APISummaryController::class, 'allRoles']);
    Route::post('/create-workgroup', [APISummaryController::class, 'CreateWorkgroups']);
    Route::post('/create-roles', [APISummaryController::class, 'CreateRoles']);

});
