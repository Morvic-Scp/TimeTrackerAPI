<?php

/**
 * @OA\Info(
 *      title="Project API",
 *      version="1.0.0",
 *      description="API documentation for managing projects"
 * )
 *
 * @OA\PathItem(path="/api")
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

 /**
 * @OA\Get(
 *     path="/user-projects",
 *     summary="Get all projects for a user",
 *     description="Returns all projects where the given user ID is assigned in `project_task`",
 *     tags={"Projects"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Parameter(
 *         name="userid",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=403, description="Unauthorized"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */



/**
     * @OA\Post(
     *     path="/create-projects",
     *     summary="Create a new project",
     *     description="Stores a new project in the database",
     *     tags={"Projects"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "duration", "public", "color","created_by"},
     *             @OA\Property(property="name", type="string", example="New Project"),
     *             @OA\Property(property="duration", type="string", example="3 months"),
     *             @OA\Property(property="public", type="boolean", example=true),
     *             @OA\Property(property="color", type="string", example="#000")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
