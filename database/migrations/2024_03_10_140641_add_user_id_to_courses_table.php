<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ƒобавл€ем столбец, который может принимать NULL
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id')->nullable()->after('id');
        });

        // ”станавливаем значение по умолчанию дл€ всех существующих курсов
        DB::table('courses')->update(['creator_id' => 10]);

        // “еперь обновл€ем столбец, чтобы он не допускал NULL и устанавливаем значение по умолчанию
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id')->default(10)->change();
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            // ≈сли вы использовали foreign key constraint, нужно его удалить
            $table->dropForeign(['creator_id']);
            $table->dropColumn('creator_id');
        });
    }
};
