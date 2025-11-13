<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class in app/Services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = str_replace('/', '\\', $name);

        $className = $this->getClassName($name);
        $namespace = $this->getNamespace($name);
        $filePath = $this->getFilePath($name);

        if (File::exists($filePath)) {
            $this->error("Service {$className} already exists!");
            return;
        }

        $this->createDirectory($filePath);
        File::put($filePath, $this->generateTemplate($className, $namespace));

        $this->info("Service {$className} created successfully in {$filePath}");
    }

    private function getClassName($name)
    {
        $parts = explode('\\', $name);
        $className = end($parts);

        return str_contains($className, 'Service') ? $className : $className . 'Service';
    }

    private function getNamespace($name)
    {
        $parts = explode('\\', $name);
        array_pop($parts); // Remover el nombre de la clase

        $namespace = 'App\\Services';
        if (!empty($parts)) {
            $namespace .= '\\' . implode('\\', $parts);
        }

        return $namespace;
    }

    private function getFilePath($name)
    {
        $className = $this->getClassName($name);
        $path = app_path('Services/');

        $parts = explode('\\', $name);
        if (count($parts) > 1) {
            array_pop($parts); // Remover el nombre de la clase
            $path .= implode('/', $parts) . '/';
        }

        return $path . $className . '.php';
    }

    private function createDirectory($filePath)
    {
        $directory = dirname($filePath);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    private function generateTemplate($className, $namespace)
    {
        return "<?php

namespace {$namespace};

class {$className}
{
    public function getAll(\$data)
    {
        // Implement logic to fetch all records
    }

    public function create(array \$data)
    {
        // Implement logic to create a new record
    }

    public function update(array \$data, \$model)
    {
        // Implement logic to update a record
    }

    public function delete(\$model)
    {
        // Implement logic to delete a record
    }
}
        ";
    }
}
