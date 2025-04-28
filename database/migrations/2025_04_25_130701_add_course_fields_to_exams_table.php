<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            // $table->string('course_code')->nullable(); ❌ REMOVE this
            $table->string('course_name')->nullable();   // ✅ Keep this only if missing
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            // $table->dropColumn('course_code'); ❌ remove this too if you're not adding it
            $table->dropColumn('course_name');
        });
    }
};
