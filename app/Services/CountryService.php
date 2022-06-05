<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psy\Readline\Hoa\Console;

class CountryService
{

    /**
     * Fetches country list from external API.
     * If country already exists in DB it skips
     */
    public static function getCountries(): bool
    {
        $countries = Country::all();
        $countryMap = [];
        foreach ($countries as $country) {
            $countryMap [$country->code] = $country;
        }


        $url = config('covid.base') . config('covid.countries');
        $countriesResponse = Http::get($url);
        if ($countriesResponse->status() != 200) {
            $message = 'Could not fetch countries from ' . $url . ' Returned status:' . $countriesResponse->status();
            ActivityLog::createLog(
                ActivityLog::FAILED,
                ActivityLog::JOB_COUNTRY,
                ActivityLog::TYPE_ERROR,
                $message);
            Log::error($message);
            return false;
        }

        $fetchedList = $countriesResponse->json();


        $insertData = [];
        DB::beginTransaction();
        try {
            foreach ($fetchedList as $item) {
                if (array_key_exists($item['code'], $countryMap)) {
                    continue;
                }

                $insertData[] = [
                    'code' => $item['code'],
                    'name' =>  json_encode($item['name'])
                ];

            }

            Country::insert($insertData);

            ActivityLog::createLog(
                ActivityLog::SUCCESS,
                ActivityLog::JOB_COUNTRY,
                ActivityLog::TYPE_INFORMATION,
                'Saved ' . count($insertData) . ' country');
            Log::info('Saved ' . count($insertData) . ' country');
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            ActivityLog::createLog(
                ActivityLog::TYPE_ERROR,
                ActivityLog::JOB_COUNTRY,
                ActivityLog::TYPE_ERROR,
                'Error occurred: ' . $e->getMessage() . ' Trace:' . $e->getTraceAsString());

            Log::error('Error occurred: ' . $e->getMessage() . ' Trace:' . $e->getTraceAsString());

            return false;
        }


        return true;

    }


}
