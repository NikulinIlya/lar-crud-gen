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
            echo('Controller has removed. ');
            unlink($path);
        }

        if (file_exists($path = app_path("/Http/Requests/{$name}Request.php"))) {
            echo('Request has removed. ');
            unlink($path);
        }

        if(!scandir($path = app_path('/Http/Requests'))) {
            rmdir($path);
        }

        if (file_exists($path = app_path("/{$name}.php"))) {
            echo('Model has removed. ');
            unlink($path);
        }

        foreach (scandir(database_path('migrations')) as $file) {
            if (stristr($file, strtolower(Str::plural($name)))) {
                unlink(database_path('migrations') . '/' . $file);
                echo('Migration has removed. ');
                break;
            }
        }

        try {
            file_put_contents($filename = base_path('routes/api.php'), str_replace(('Route::resource(\'' . Str::plural(strtolower($name)) . "', '{$name}Controller');"), "", file_get_contents($filename)));
            echo('Api has modified. ');
        } catch (Exception $e) {
            echo $e;
        }
    }
}
