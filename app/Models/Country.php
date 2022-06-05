<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property Statistics currentStatistic
 */
class Country extends Model
{
    use HasFactory;


    /**
     * @param $countries
     * @return array
     * Converts Country object to displayable array
     */
    public static function getListData($countries): array
    {
        $data = [];
        foreach ($countries as $country) {
            $data[$country->code] = $country->getData();
        }
        return $data;
    }


    /**
     * @return array
     * Formats current object and returns to array
     */
    public function getData(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => json_decode($this->name, true)
        ];
    }

    public function currentStatistic(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        $current = date('Y-m-d');
        return $this->hasOne(Statistics::class)->whereDate(DB::raw("DATE_FORMAT(`created_at`, '%Y-%m-%d')"), '=', "$current");
    }

}
