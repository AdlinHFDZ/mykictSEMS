<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**tos */
    /**  public function up()
     {
         Schema::table('exams', function (Blueprint $table) {
             $table->json('tos')->nullable();
             $table->string('pdf_path')->nullable();
         });
     }*/


    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('course_name');
            $table->string('course_id');
            $table->string('section');
            $table->text('questions')->nullable(); // JSON or serialized format
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};