<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function stats(Request $request)
    {
        $parameters = [
            'search' => $request->get('search'),
            'column' => $request->get('column'),
            'order' => $request->get('order'),
            'offset' => $request->get('offset'),
            'limit' => $request->get('limit'),
        ];


        $response = StatisticsService::getStats($parameters);

        if(!$response['total']){
            return response()->json(['success' => false, 'message' => 'Data not found', 'data' => []], 204);
        }

        return response()->json(['success' => true, 'data' => $response]);

    }


    public function summary(Request $request)
    {
        $data = StatisticsService::getSummary();

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data not found', 'data' => []], 204);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
