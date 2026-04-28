<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceQrCodesTable extends Migration
{
    public function up()
    {
        Schema::create('attendance_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('qr_code')->unique();
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_qr_codes');
    }
}