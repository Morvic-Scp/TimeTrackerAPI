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
        Schema::create('user_workgroups',function(Blueprint $table){
            $table->id();
            $table->foreignId('userid')->references('id')->on('users');
            $table -> foreignId('workgroupid')->references('id')->on('workgroups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_workgroups');
    }
};
