<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->index(); // admin, manager, supervisor, intern[cite: 7, 8]
            $table->json('assigned_modules')->nullable(); 
            $table->longText('image')->nullable();

            // Legacy ID Mapping to maintain foreign key compatibility[cite: 2, 4]
            $table->unsignedBigInteger('legacy_admin_id')->nullable();
            $table->unsignedBigInteger('legacy_manager_id')->nullable();
            $table->unsignedBigInteger('legacy_intern_id')->nullable();

            // Fields from ManagersAccount[cite: 2]
            $table->string('eti_id')->nullable()->index();
            $table->string('contact')->nullable();
            $table->string('department')->nullable();
            $table->decimal('comission', 8, 2)->nullable();

            // Fields from InternAccount[cite: 4]
            $table->string('int_technology')->nullable();
            $table->string('portal_status')->default('active'); 
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('users'); }
};