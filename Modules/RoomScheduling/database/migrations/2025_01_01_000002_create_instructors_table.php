<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("instructors", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->string("employee_no")->nullable()->unique();
            $table->string("first_name");
            $table->string("last_name");
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("instructors");
    }
};
