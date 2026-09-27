<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("rooms", function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->string("name");
            $table->string("building")->nullable();
            $table->string("floor")->nullable();
            $table->unsignedInteger("capacity")->default(0);
            $table->enum("type", ["lecture", "laboratory", "hybrid"])->default("lecture");
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("rooms");
    }
};
