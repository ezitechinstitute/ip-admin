<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('chats', function (Blueprint $table) {
        if (!Schema::hasColumn('chats', 'created_by')) {
            $table->unsignedBigInteger('created_by')->after('project_id')->nullable();
            $table->string('chat_type')->after('created_by')->default('project');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            //
        });
    }
};
