<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("sections", function (Blueprint $table) {
            $table->id();
            $table->foreignId("subject_id")->constrained("subjects")->cascadeOnDelete();
            $table->string("section_code");
            $table->string("school_year");
            $table->string("semester");
            $table->unsignedInteger("max_students")->default(40);
            $table->timestamps();

            $table->unique(["subject_id", "section_code", "school_year", "semester"], "sections_unique_combo");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("sections");
    }
};
