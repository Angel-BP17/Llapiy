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
    public function clearAll(): void
    {
        try {
            Schema::disableForeignKeyConstraints();

            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

            foreach ($tables as $table) {
                if (!in_array($table, ['migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens'])) {
                    DB::table($table)->truncate();
                }
            }

            Schema::enableForeignKeyConstraints();

            $documentsPath = storage_path('app/public/documents');
            $profilePath = storage_path('app/public/usuarios/perfiles');
            $blocksPath = storage_path('app/public/blocks');

            if (File::exists($documentsPath)) {
                File::deleteDirectory($documentsPath);
                File::makeDirectory($documentsPath, 0755, true);
            }

            if (File::exists($profilePath)) {
                File::deleteDirectory($profilePath);
                File::makeDirectory($profilePath);
            }

            if (File::exists($blocksPath)) {
                File::deleteDirectory($blocksPath);
                File::makeDirectory($blocksPath);
            }

            Artisan::call('db:seed', ['--force' => true]);
        } catch (Exception $e) {
            Log::error('Error en clearAll(): ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }
}

