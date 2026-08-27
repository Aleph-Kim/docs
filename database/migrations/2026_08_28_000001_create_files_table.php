<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fileable_id')->nullable();
            $table->string('fileable_type');
            $table->string('field_name');
            $table->string('url');
            $table->string('name');
            $table->string('origin_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['fileable_type', 'fileable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
