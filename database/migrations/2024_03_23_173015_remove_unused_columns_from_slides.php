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
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('subtitle');
            $table->dropColumn('button_text');
            $table->dropColumn('has_exit_button');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            // ��� ����� ������������� ������� ��� � ������ �������, ���� �� ��� �� �����������
            $table->integer('position')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->string('audio_path')->nullable();
            $table->boolean('has_exit_button')->default(false);
        });
    }
};
