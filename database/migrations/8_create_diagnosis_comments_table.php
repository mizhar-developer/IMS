<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('diagnosis_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_id')->constrained('diagnoses')->onDelete('cascade');
            $table->string('user_type')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('content')->nullable();
            $table->foreignId('image_id')->nullable()->constrained('medical_images')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('diagnosis_comments');
    }
};
