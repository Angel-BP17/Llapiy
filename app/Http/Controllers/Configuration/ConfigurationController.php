<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Services\Home\BackupService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ConfigurationController extends Controller
{
    public function __construct(protected BackupService $service)
    {
    }

    /**
     * Display the configuration page.
     */
    public function index(): Response
    {
        return Inertia::render('configuration/index');
    }

    /**
     * Export a system backup.
     */
    public function export()
    {
        try {
            $path = $this->service->createBackup();
            return response()->download($path)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al exportar backup: ' . $e->getMessage());
        }
    }

    /**
     * Import a system backup.
     */
    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip',
        ]);

        try {
            $file = $request->file('backup_file');
            $path = $file->storeAs('temp', 'import_backup.zip');
            
            $this->service->restoreBackup(storage_path("app/{$path}"));
            
            Storage::delete($path);

            return redirect()->back()->with('message', 'Sistema restaurado exitosamente.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al importar backup: ' . $e->getMessage());
        }
    }

    /**
     * Update the user's theme setting.
     */
    public function updateTheme(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'theme' => 'required|string|in:light,dark',
        ]);

        $request->user()->settings()->updateOrCreate(
            ['key' => 'theme'],
            ['value' => $request->theme]
        );

        return redirect()->back()->with('message', 'Tema actualizado correctamente.');
    }
}
