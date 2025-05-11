<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->date('exam_date')->nullable()->after('status');
            $table->string('exam_time')->nullable()->after('exam_date');
            $table->string('duration')->nullable()->after('exam_time');
            $table->text('instruction')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['exam_date', 'exam_time', 'duration', 'instruction']);
        });
    }
};
