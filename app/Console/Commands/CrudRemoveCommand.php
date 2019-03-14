<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrudRemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:remove {name : Class (singular), e.g User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove created CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (file_exists($path = app_path("/Http/Controllers/{$name}Controller.php"))) {
            echo("{$name}Controller has removed.\n");
            unlink($path);
        }

        if (file_exists(app_path("/Http/Requests"))) {
            $this->removeRequest($name);
        }

        if (file_exists($path = app_path("/{$name}.php"))) {
            echo("{$name} model has removed.\n");
            unlink($path);
        }

        foreach (scandir(database_path('migrations')) as $file) {
            if (stristr($file, strtolower(Str::plural($name)))) {
                unlink(database_path('migrations') . '/' . $file);
                echo("{$name} migration has removed.\n");
                break;
            }
        }

        try {
            file_put_contents($filename = base_path('routes/api.php'), str_replace(('Route::resource(\'' . Str::plural(strtolower($name)) . "', '{$name}Controller');"), "", file_get_contents($filename)));
            echo('Routes have rolled back.' . "\n");
        } catch (Exception $e) {
            echo $e;
        }
    }

    private function removeRequest($name)
    {
        if (file_exists($path = app_path("/Http/Requests/{$name}Request.php"))) {
            echo("{$name}Request has removed.\n");
            unlink($path);
        }

        $hasRequestFile = false;
        foreach (scandir($path = app_path('/Http/Requests')) as $file) {
            if (stristr($file, 'Request')) {
                $hasRequestFile = true;
                break;
            }
        }

        if (!$hasRequestFile) {
            rmdir($path);
            echo("Request folder has removed.\n");
        } else {
            echo("Request folder hasn\'t removed.\n");
        }
    }
}
