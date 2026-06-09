<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) NOT NULL DEFAULT 'puskesmas'");

        Schema::table('users', function (Blueprint $table) {
            $table->string('puskesmas')->nullable()->after('role');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->string('puskesmas')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('puskesmas');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('puskesmas');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'nurse') NOT NULL DEFAULT 'nurse'");
    }
};
