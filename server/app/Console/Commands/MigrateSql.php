<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class MigrateSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all SQL scripts added';

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
     * @return int
     */
    public function handle()
    {
        $scriptsDirectory = database_path('sql-scripts');
        $files = File::allFiles($scriptsDirectory);
        foreach($files as $file) {
            $sql = $file->getContents();
            if(!empty($sql))
            DB::connection('mysql')->unprepared($sql);
        }
        return 1;
    }
}
