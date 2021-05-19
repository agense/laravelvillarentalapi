<?php
/**
 * Command calls php artisan route:list to read api routes and writes a list of routes to a file as json.
 * Allows route grouping and decription generation.
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\ApiRouteWriterService;
use Artisan;

class CreateRoutesFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:to-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a json file with api routes';

    /**
     * Path to the json file to be created in storage
     */
    protected $filePath;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->filePath = "/data/routes.json";
    }

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(ApiRouteWriterService $service)
    {
        //Get all routes in json
        Artisan::call('route:list --columns=method,uri --path=api --json');
        $data = Artisan::output();

        //Add automatic descriptions to routes
        if(config('commands.route_display.route_autodescription')){
            $data = $service->describeRoutes($data);
        }

        //Group routes
        $data = $service->groupRoutes($data);

        if(getType($data) !== "String") {
            $data = json_encode($data);
        }

        if(Storage::put($this->filePath, $data)){
            $this->info('File created successfuly.');
        }else{
            $this->error('Error - file creation failed.');
        }
    }
}
