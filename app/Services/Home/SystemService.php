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

            $driver = DB::connection()->getDriverName();
            $database = DB::connection()->getDatabaseName();
            $tables = [];
            foreach (Schema::getTables() as $t) {
                $schema = is_array($t) ? $t['schema'] : $t->schema;
                $name = is_array($t) ? $t['name'] : $t->name;
                if ($driver === 'sqlite' || $schema === $database) {
                    $tables[] = $name;
                }
            }

            $protectedTables = ['migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens'];

            foreach ($tables as $table) {
                if (! in_array($table, $protectedTables)) {
                    DB::table($table)->truncate();
                }
            }

            Schema::enableForeignKeyConstraints();

            // Limpieza de directorios físicos de documentos, bloques y perfiles (tanto en storage privado como público)
            $paths = [
                // Almacenamiento privado (disco local predeterminado de la app)
                storage_path('app/private/documents'),
                storage_path('app/private/blocks'),
                storage_path('app/private/usuarios/perfiles'),
                storage_path('app/private/usuarios'),
                storage_path('app/private/temp'),

                // Almacenamiento público (disco public)
                storage_path('app/public/documents'),
                storage_path('app/public/blocks'),
                storage_path('app/public/usuarios/perfiles'),
                storage_path('app/public/usuarios'),
            ];

            foreach ($paths as $path) {
                if (File::exists($path)) {
                    File::cleanDirectory($path);
                } else {
                    File::makeDirectory($path, 0755, true);
                }
            }

            // Limpieza de residuos temporales de restauración
            $tempRestore = storage_path('app/temp_restore');
            if (File::exists($tempRestore)) {
                File::deleteDirectory($tempRestore);
            }

            Artisan::call('db:seed', ['--force' => true]);
        } catch (Exception $e) {
            Log::error('Fallo crítico en SystemService::clearAll(): '.$e->getMessage());
            throw $e;
        }
    }
}
