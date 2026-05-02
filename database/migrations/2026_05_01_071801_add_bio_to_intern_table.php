<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBioToInternTable extends Migration
{
    public function up()
    {
        Schema::table('intern_table', function (Blueprint $table) {
            if (!Schema::hasColumn('intern_table', 'bio')) {
                $table->text('bio')->nullable()->after('intern_type');
            }
        });
    }

    public function down()
    {
        Schema::table('intern_table', function (Blueprint $table) {
            if (Schema::hasColumn('intern_table', 'bio')) {
                $table->dropColumn('bio');
            }
        });
    }
}