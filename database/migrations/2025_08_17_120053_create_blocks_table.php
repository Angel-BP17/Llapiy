<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Laravel\Prompts\table;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('n_bloque');
            $table->string('asunto');
            $table->string('folios');
            $table->string('rango_inicial');
            $table->string('rango_final');
            $table->string('root');
            $table->date('fecha');
            $table->year('periodo');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('box_id')->nullable()->default(null)->constrained('boxes')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
            $table->foreignId('subgroup_id')->nullable()->constrained('subgroups')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['n_bloque', 'periodo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
