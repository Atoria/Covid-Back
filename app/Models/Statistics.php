<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $critical
 * @property int $recovered
 * @property int $confirmed
 * @property int $deaths
 * @property int $country_id
 * @property Country $country
 */
class Statistics extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'updated_at',
        'created_at'
    ];


    /**
     * @param $date
     * @param null $limit
     * @param null $offset
     * @return array
     * Returns statistics data by chosen date
     */
    public static function getStatisticsByDate($date, $limit = null, $offset = null): array
    {
        $query = new Statistics;
        if ($limit && $offset) {
            $query = $query->skip($offset)->take($limit);
        }

        $query = $query->where(DB::raw("DATE_FORMAT(`created_at`, '%Y-%m-%d')"), '=', "$date");

        $total = $query->count();
        $statistics = $query->get();

        $data = [];

        foreach ($statistics as $statistic) {
            $data[$statistic->country_id] = $statistic->getData();
        }

        return [
            'data' => $data,
            'total' => $total
        ];


    }


    /**
     * @return array
     * Returns formatted data
     */
    public function getData()
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'country' => $this->country_id ? $this->country->getData() : null,
            'confirmed' => $this->confirmed,
            'recovered' => $this->recovered,
            'critical' => $this->critical,
            'deaths' => $this->deaths
        ];
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }


}
