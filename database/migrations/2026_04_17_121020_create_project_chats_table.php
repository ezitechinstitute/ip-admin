<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_chats', function (Blueprint $table) {
            $table->id(); // This remains as standard primary key
            
            // Changed to standard integer to perfectly match int(11) in intern_projects
            $table->integer('project_id'); 
            
            $table->timestamps();

            // The foreign key will now connect perfectly
            $table->foreign('project_id')
                  ->references('project_id')
                  ->on('intern_projects')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_chats');
    }
};