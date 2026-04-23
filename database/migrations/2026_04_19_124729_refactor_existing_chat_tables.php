<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 1. Repurpose 'project_chats' to 'chats'
        if (Schema::hasTable('project_chats') && !Schema::hasTable('chats')) {
            Schema::rename('project_chats', 'chats');
        }

        // 2. Handle the 'messages' table conflict
        if (Schema::hasTable('chat_messages') && !Schema::hasTable('messages')) {
            Schema::rename('chat_messages', 'messages');
        } 
        // If 'chats' was your old messages table, move it to 'messages'
        elseif (Schema::hasTable('chats') && !Schema::hasTable('messages')) {
            Schema::rename('chats', 'messages');
        }

        // 3. Create 'chat_participants' (The manual access control table)
        if (!Schema::hasTable('chat_participants')) {
            Schema::create('chat_participants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                if (Schema::hasTable('chats')) {
                    $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
                }
            });
        }

        // 4. Final adjustments to the messages table
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'chat_id')) {
                    $table->unsignedBigInteger('chat_id')->after('id')->nullable();
                }
                if (Schema::hasColumn('messages', 'user_id') == false && Schema::hasColumn('messages', 'sender_id')) {
                    $table->renameColumn('sender_id', 'user_id');
                }
            });
        }
    }

    public function down() {
        Schema::dropIfExists('chat_participants');
    }
};