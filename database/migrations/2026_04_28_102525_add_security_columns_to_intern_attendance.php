<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecurityColumnsToInternAttendance extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('intern_attendance', function (Blueprint $table) {
            // Check-in security columns
            $table->string('checkin_method', 50)->nullable()->after('status');
            $table->string('checkin_qr_code', 255)->nullable()->after('checkin_method');
            $table->decimal('checkin_latitude', 10, 8)->nullable()->after('checkin_qr_code');
            $table->decimal('checkin_longitude', 10, 8)->nullable()->after('checkin_latitude');
            $table->decimal('checkin_accuracy', 10, 2)->nullable()->after('checkin_longitude');
            
            // Check-out security columns
            $table->string('checkout_method', 50)->nullable()->after('duration');
            $table->decimal('checkout_latitude', 10, 8)->nullable()->after('checkout_method');
            $table->decimal('checkout_longitude', 10, 8)->nullable()->after('checkout_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('intern_attendance', function (Blueprint $table) {
            $table->dropColumn([
                'checkin_method',
                'checkin_qr_code',
                'checkin_latitude',
                'checkin_longitude',
                'checkin_accuracy',
                'checkout_method',
                'checkout_latitude',
                'checkout_longitude',
            ]);
        });
    }
}