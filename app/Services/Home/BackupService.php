<?php

namespace App\Services\Home;

use DB;
use Exception;
use File;
use Log;
use ZipArchive;

class BackupService
{
    protected $backupPath;
    protected $storagePaths = [
        'documents' => 'app/public/documents',
        'perfiles' => 'app/public/usuarios/perfiles',
        'blocks' => 'app/public/blocks'
    ];

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Crea un respaldo completo del sistema.
     */
    public function createBackup(): string
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $zipFileName = "backup_{$timestamp}.zip";
            $zipPath = "{$this->backupPath}/{$zipFileName}";

            $sqlFile = $this->generateSqlDump();

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception("No se pudo crear el archivo ZIP.");
            }

            // Añadir base de datos
            $zip->addFile($sqlFile, 'database.sql');

            // Añadir archivos físicos
            foreach ($this->storagePaths as $folder => $relPath) {
                $fullPath = storage_path($relPath);
                if (File::exists($fullPath)) {
                    $files = File::allFiles($fullPath);
                    foreach ($files as $file) {
                        $zip->addFile($file->getRealPath(), "storage/{$folder}/" . $file->getRelativePathname());
                    }
                }
            }

            $zip->close();
            File::delete($sqlFile);

            return $zipPath;
        } catch (Exception $e) {
            Log::error('Error al crear backup: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Restaura un respaldo desde un archivo ZIP.
     */
    public function restoreBackup(string $zipPath): void
    {
        try {
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new Exception("No se pudo abrir el archivo de respaldo.");
            }

            $tempPath = storage_path('app/temp_restore');
            if (File::exists($tempPath)) {
                File::deleteDirectory($tempPath);
            }
            File::makeDirectory($tempPath, 0755, true);

            $zip->extractTo($tempPath);
            $zip->close();

            // 1. Restaurar Base de Datos
            $sqlFile = "{$tempPath}/database.sql";
            if (File::exists($sqlFile)) {
                $this->restoreSqlDump($sqlFile);
            }

            // 2. Restaurar Archivos
            foreach ($this->storagePaths as $folder => $relPath) {
                $src = "{$tempPath}/storage/{$folder}";
                $dest = storage_path($relPath);

                if (File::exists($src)) {
                    if (!File::exists($dest)) {
                        File::makeDirectory($dest, 0755, true);
                    }
                    File::cleanDirectory($dest);
                    File::copyDirectory($src, $dest);
                }
            }

            File::deleteDirectory($tempPath);
            
            // Limpiar caché de la aplicación
            \Artisan::call('cache:clear');
            
        } catch (Exception $e) {
            Log::error('Error al restaurar backup: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Genera un volcado SQL básico compatible con MySQL.
     */
    protected function generateSqlDump(): string
    {
        $sqlFile = "{$this->backupPath}/temp_db.sql";
        $handle = fopen($sqlFile, 'w+');

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $key = "Tables_in_{$dbName}";

        // Desactivar llaves foráneas
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $table) {
            $tableName = $table->$key;
            
            // Ignorar tablas de sistema si es necesario (opcional)
            // if (in_array($tableName, ['migrations'])) continue;

            // Estructura
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0]->{'Create Table'};
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, $createTable . ";\n\n");

            // Datos
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $columns = array_keys($rowArray);
                $values = array_values($rowArray);

                $escapedValues = array_map(function($val) {
                    if (is_null($val)) return 'NULL';
                    return "'" . addslashes($val) . "'";
                }, $values);

                $sql = "INSERT INTO `{$tableName}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
                fwrite($handle, $sql);
            }
            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return $sqlFile;
    }

    /**
     * Ejecuta el volcado SQL.
     */
    protected function restoreSqlDump(string $path): void
    {
        DB::unprepared(File::get($path));
    }
}
