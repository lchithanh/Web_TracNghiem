<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('password');
            }

            if (!Schema::hasColumn('users', 'student_code')) {
                $table->string('student_code', 20)->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('student_code');
            }
        });

        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->whereNull('role')->update(['role' => 'student']);
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_code')) {
                try {
                    $table->unique('student_code');
                } catch (\Throwable $e) {
                    // Ignore if unique index already exists.
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropUnique(['student_code']);
            } catch (\Throwable $e) {
                // Ignore if index does not exist.
            }

            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('users', 'student_code')) {
                $table->dropColumn('student_code');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};

