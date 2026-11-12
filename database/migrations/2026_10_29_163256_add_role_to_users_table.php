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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('password');
            }
        });

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            \Illuminate\Support\Facades\DB::table('users')->where('role_id', 2)->update(['role' => 'admin']);
            \Illuminate\Support\Facades\DB::table('users')->where('role_id', 1)->update(['role' => 'user']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
