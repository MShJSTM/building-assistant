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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('project_type');
            $table->text('address');
            $table->string('postal_plate');
            $table->integer('land_area');
            $table->integer('building_area');
            $table->string('structure_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('permit_start_date');
            $table->date('permit_end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
