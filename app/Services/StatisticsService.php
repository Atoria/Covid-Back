<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Country;
use App\Models\Statistics;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatisticsService
{

    /**
     * Fetches statistics from external API.
     * If country record already exists in DB it skips
     */
    public static function getStatistics(): bool
    {
        $today = date('Y-m-d');
        $countryList = Country::all();
        $countryMap = Country::getListData($countryList);


        try {

            foreach ($countryList as $country) {
                $url = config('covid.base') . config('covid.statistics');
                $statisticsResponse = Http::post($url, [
                    'code' => $country->code
                ]);

                if ($statisticsResponse->status() != 200) {
                    $message = 'Could not fetch statistics from ' . $url . ' Returned status:' . $statisticsResponse->status();
                    ActivityLog::createLog(
                        ActivityLog::FAILED,
                        ActivityLog::JOB_COUNTRY,
                        ActivityLog::TYPE_ERROR,
                        $message);
                    Log::error($message);
                    continue;
                }

                $fetchedItem = $statisticsResponse->json();

                $statistic = $country->currentStatistic ? $country->currentStatistic : new Statistics();
                $statistic->country_id = $countryMap[$country->code]['id'];
                $statistic->confirmed = $fetchedItem['confirmed'];
                $statistic->recovered = $fetchedItem['recovered'];
                $statistic->critical = $fetchedItem['critical'];
                $statistic->deaths = $fetchedItem['deaths'];
                if (!$statistic->save()) {
                    $message = 'Could not save statistic for country: ' . $country->code;
                    ActivityLog::createLog(
                        ActivityLog::FAILED,
                        ActivityLog::JOB_COUNTRY,
                        ActivityLog::TYPE_ERROR,
                        $message);
                    Log::error($message);
                }
            }


            ActivityLog::createLog(
                ActivityLog::SUCCESS,
                ActivityLog::JOB_STATISTIC,
                ActivityLog::TYPE_INFORMATION,
                'Statistics saved successfully');
            Log::info('Statistics saved successfully');
        } catch (\Exception $e) {
            ActivityLog::createLog(
                ActivityLog::TYPE_ERROR,
                ActivityLog::JOB_STATISTIC,
                ActivityLog::TYPE_ERROR,
                'Error occurred: ' . $e->getMessage() . ' Trace:' . $e->getTraceAsString());
            Log::error('Error occurred: ' . $e->getMessage() . ' Trace:' . $e->getTraceAsString());
            return false;
        }

        return true;
    }


    /**
     * @return mixed
     * Returns total number of statistics
     */
    public static function getSummary(): mixed
    {
        return Statistics::select(
            DB::raw('SUM(confirmed) as confirmed'),
            DB::raw('SUM(recovered) as recovered'),
            DB::raw('SUM(critical) as critical'),
            DB::raw('SUM(deaths) as deaths')
        )->get()->toArray();

    }


    /**
     *Returns data per country
     */
    public static function getStats($parameters)
    {
        $query = Country::select(
            'countries.code',
            'countries.name',
            DB::raw('SUM(confirmed) as confirmed'),
            DB::raw('SUM(recovered) as recovered'),
            DB::raw('SUM(critical) as critical'),
            DB::raw('SUM(deaths) as deaths')
        )
            ->leftJoin('statistics', 'countries.id', '=', 'statistics.country_id');

        if ($parameters['column']  && $parameters['order']) {
            $query = $query->orderBy($parameters['column'], $parameters['order']);
        }
        $query = $query->groupBy('countries.code');

        $total = $query->get()->count();

        if ($parameters['search']) {
            $query = $query->orWhere('countries.code', '=', $parameters['search'])
                ->orWhere('countries.name', '=', $parameters['search']);
        }


        if ($parameters['offset'] != null && $parameters['limit'] != null) {
            $query = $query->skip($parameters['offset'])->take($parameters['limit']);
        }

        $data = $query->get()->toArray();

        return [
            'total' => $total,
            'data' => $data
        ];

    }


}
