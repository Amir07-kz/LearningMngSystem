<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ��������� �������, ������� ����� ��������� NULL
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id')->nullable()->after('id');
        });

        // ������������� �������� �� ��������� ��� ���� ������������ ������
        DB::table('courses')->update(['creator_id' => 10]);

        // ������ ��������� �������, ����� �� �� �������� NULL � ������������� �������� �� ���������
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id')->default(10)->change();
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            // ���� �� ������������ foreign key constraint, ����� ��� �������
            $table->dropForeign(['creator_id']);
            $table->dropColumn('creator_id');
        });
    }
};
