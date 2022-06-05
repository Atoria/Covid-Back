<?php

namespace App\Console\Commands;

use App\Services\CountryService;
use Illuminate\Console\Command;

class FetchCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetch-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching countries from external API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return CountryService::getCountries();
    }
}
