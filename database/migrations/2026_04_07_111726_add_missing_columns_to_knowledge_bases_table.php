<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            // Add missing columns after content
            $table->string('file_path', 500)->nullable()->after('content');
            $table->string('video_url', 500)->nullable()->after('file_path');
            $table->string('external_link', 500)->nullable()->after('video_url');
            $table->string('thumbnail', 500)->nullable()->after('external_link');
            $table->json('tags')->nullable()->after('thumbnail');
            $table->integer('views')->default(0)->after('tags');
            $table->integer('downloads')->default(0)->after('views');
            $table->boolean('is_featured')->default(false)->after('downloads');
            $table->integer('order_position')->default(0)->after('is_featured');
        });
    }

    public function down()
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropColumn([
                'file_path',
                'video_url',
                'external_link',
                'thumbnail',
                'tags',
                'views',
                'downloads',
                'is_featured',
                'order_position'
            ]);
        });
    }
};