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
        Schema::create('workgroup_projects',function(Blueprint $table){
            $table->id();
            $table -> foreignId('projectid')->references('id')->on('projects');
            $table -> foreignId('workgroupid')->nullable()->references('id')->on('workgroups');
            $table->foreignId('userid')->nullable()->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workgroup_projects');
    }
};
