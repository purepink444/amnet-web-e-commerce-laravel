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
<<<<<<<< HEAD:database/migrations/0001_01_01_000001_create_roles_table.php
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->enum('role_name', ['admin', 'member'])->unique();
            $table->text('description')->nullable();
========
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
>>>>>>>> 0afe553688140d733a192b68935aae661698b5d7:database/migrations/2025_11_18_024150_create_brands_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/0001_01_01_000001_create_roles_table.php
        Schema::dropIfExists('roles');
========
        Schema::dropIfExists('brands');
>>>>>>>> 0afe553688140d733a192b68935aae661698b5d7:database/migrations/2025_11_18_024150_create_brands_table.php
    }
};