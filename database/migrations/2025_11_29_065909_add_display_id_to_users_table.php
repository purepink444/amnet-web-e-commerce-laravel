<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('display_id')->nullable()->after('user_id');
        });

        // Assign sequential display_ids to existing users
        $users = DB::table('users')->orderBy('user_id')->get();
        $displayId = 1;
        foreach ($users as $user) {
            DB::table('users')->where('user_id', $user->user_id)->update(['display_id' => $displayId]);
            $displayId++;
        }

        // Add unique constraint after populating data
        Schema::table('users', function (Blueprint $table) {
            $table->unique('display_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('display_id');
        });
    }
};
