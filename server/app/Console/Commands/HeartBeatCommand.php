<?php

namespace App\Console\Commands;

use App\Http\Services\HeartBeatService;
use Illuminate\Console\Command;

class HeartBeatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heartBeat:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'HeartBeats Cron Job';
    private $heartBeatService_;
    public function __construct(HeartBeatService $heartBeatService)
    {
        $this->heartBeatService_ = $heartBeatService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $this->heartBeatService_->heartBeatCheckAll();
        
    }
}
