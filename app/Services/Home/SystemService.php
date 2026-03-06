<?php

namespace App\Services\Home;

use Artisan;
use DB;
use Exception;
use File;
use Log;
use Schema;

class SystemService
{
    /**
     * Limpia completamente la base de datos y el almacenamiento físico, 
     * dejando el sistema en su estado inicial (Seeding).
     */
    public function clearAll(): void
    {
        try {
            Schema::disableForeignKeyConstraints();

            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $protectedTables = ['migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens'];

            foreach ($tables as $table) {
                if (!in_array($table, $protectedTables)) {
                    DB::table($table)->truncate();
                }
            }

            Schema::enableForeignKeyConstraints();

            // Limpieza de directorios físicos de documentos y perfiles
            $paths = [
                storage_path('app/public/documents'),
                storage_path('app/public/usuarios/perfiles'),
                storage_path('app/public/blocks')
            ];

            foreach ($paths as $path) {
                if (File::exists($path)) {
                    File::cleanDirectory($path);
                } else {
                    File::makeDirectory($path, 0755, true);
                }
            }

            Artisan::call('db:seed', ['--force' => true]);
        } catch (Exception $e) {
            Log::error('Fallo crítico en SystemService::clearAll(): ' . $e->getMessage());
            throw $e;
        }
    }
}
