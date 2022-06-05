<?php

namespace App\Console\Commands;

use App\Services\StatisticsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetch-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command which is executed from Kernel to fetch statistics for each county';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return StatisticsService::getStatistics();
    }
}
