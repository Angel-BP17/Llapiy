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
        Schema::table('campo_types', function (Blueprint $table) {
            if (!Schema::hasColumn('campo_types', 'data_type')) {
                $table->string('data_type')->default('string')->after('name');
            }
            if (!Schema::hasColumn('campo_types', 'is_nullable')) {
                $table->boolean('is_nullable')->default(true)->after('data_type');
            }
            if (!Schema::hasColumn('campo_types', 'length')) {
                $table->unsignedInteger('length')->nullable()->after('is_nullable');
            }
            if (!Schema::hasColumn('campo_types', 'allow_negative')) {
                $table->boolean('allow_negative')->default(false)->after('length');
            }
            if (!Schema::hasColumn('campo_types', 'allow_zero')) {
                $table->boolean('allow_zero')->default(true)->after('allow_negative');
            }
            if (!Schema::hasColumn('campo_types', 'enum_values')) {
                $table->json('enum_values')->nullable()->after('allow_zero');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campo_types', function (Blueprint $table) {
            $columns = [
                'data_type',
                'is_nullable',
                'length',
                'allow_negative',
                'allow_zero',
                'enum_values',
            ];

            $dropColumns = array_values(array_filter($columns, fn($column) => Schema::hasColumn('campo_types', $column)));
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};

