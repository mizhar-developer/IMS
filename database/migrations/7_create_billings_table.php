<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->decimal('adjustment', 10, 2)->default(0)->after('amount');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('billings');
    }
};
