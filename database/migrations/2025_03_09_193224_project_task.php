<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_task',function (Blueprint $table){
            $table->id();
            $table->foreignId('projectid')->references('id')->on('projects');
            $table->foreignId('created_by')->references('id')->on('users');
            $table->foreignId('workgroupid')->references('id')->on('workgroups')->nullable();
            $table->timestamp('startTimeDate');
            $table->timestamp('endTimeDate');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task');
    }
};
