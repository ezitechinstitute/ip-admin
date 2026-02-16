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
         Schema::create('supervisor_leaves', function (Blueprint $table) {

            $table->id('leave_id');                 // matches your primary key
            $table->string('supervisor_id');        // was varchar(255)
            $table->string('name');
            $table->string('email');
            $table->date('from_date');
            $table->date('to_date');
            $table->text('reason');
            $table->integer('days');
            $table->tinyInteger('leave_status')->nullable();

            // Laravel style timestamps (equivalent to your current datetime setup)
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_leaves');
    }
};
