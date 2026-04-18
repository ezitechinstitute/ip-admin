<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            
            // Polymorphic columns to handle Admins, Managers, and Interns
            $table->string('sender_type'); // Will store the Model name (e.g., App\Models\InternAccount)
            $table->unsignedBigInteger('sender_id'); // Will store the int_id, manager_id, or admin id
            
            $table->text('message'); // The actual text message
            $table->timestamps();

            $table->foreign('chat_id')->references('id')->on('project_chats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};