<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campo_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('data_type')->default('string');
            $table->boolean('is_nullable')->default(true);
            $table->unsignedInteger('length')->nullable();
            $table->boolean('allow_negative')->default(false);
            $table->boolean('allow_zero')->default(true);
            $table->json('enum_values')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campo_types');
    }
};
