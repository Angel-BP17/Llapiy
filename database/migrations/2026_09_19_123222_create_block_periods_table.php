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
        Schema::create('block_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('blocks')->cascadeOnDelete();
            $table->integer('rango_inicial');
            $table->integer('rango_final');
            $table->integer('periodo');
            $table->timestamps();

            $table->index(['block_id', 'periodo']);
            $table->index('periodo');
        });

        // Migrar datos de bloques existentes
        $existingBlocks = DB::table('blocks')->select(['id', 'rango_inicial', 'rango_final', 'periodo', 'fecha'])->get();
        $now = now();
        $inserts = [];
        foreach ($existingBlocks as $b) {
            $periodo = $b->periodo ?? ($b->fecha ? \Carbon\Carbon::parse($b->fecha)->year : null);
            if ($periodo && is_numeric($b->rango_inicial) && is_numeric($b->rango_final)) {
                $inserts[] = [
                    'block_id' => $b->id,
                    'rango_inicial' => (int) $b->rango_inicial,
                    'rango_final' => (int) $b->rango_final,
                    'periodo' => (int) $periodo,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        if (! empty($inserts)) {
            DB::table('block_periods')->insert($inserts);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_periods');
    }
};
