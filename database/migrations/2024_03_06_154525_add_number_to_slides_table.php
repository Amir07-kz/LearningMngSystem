<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Slide;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->integer('slide_number')->nullable();
        });

        Slide::all()->each(function (Slide $slide) {
            $maxNumber = Slide::where('course_id', $slide->course_id)->max('slide_number') ?? 0;
            $slide->slide_number = $maxNumber + 1;
            $slide->save();
        });

        // Шаг 3: Изменить столбец на NOT NULL
        Schema::table('slides', function (Blueprint $table) {
            $table->integer('slide_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn('slide_number');
        });
    }
};

