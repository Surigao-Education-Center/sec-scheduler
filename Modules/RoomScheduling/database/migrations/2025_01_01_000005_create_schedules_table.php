<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("schedules", function (Blueprint $table) {
            $table->id();
            $table->foreignId("section_id")->constrained("sections")->cascadeOnDelete();
            $table->foreignId("room_id")->constrained("rooms")->cascadeOnDelete();
            $table->foreignId("instructor_id")->constrained("instructors")->cascadeOnDelete();

            $table->enum("day", [
                "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday",
            ]);
            $table->time("start_time");
            $table->time("end_time");

            $table->string("school_year");
            $table->string("semester");

            $table->timestamps();

            $table->index(["day", "school_year", "semester"]);
            $table->index(["room_id", "day"]);
            $table->index(["instructor_id", "day"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("schedules");
    }
};
