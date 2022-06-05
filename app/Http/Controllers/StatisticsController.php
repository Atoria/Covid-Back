<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function stats(Request $request)
    {

    }


    public function summary(Request $request)
    {
        $data = StatisticsService::GetSummary();

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data not found', 'data' => []], 204);
        }

        return response()->json(['success' => true, 'data' =>$data]);
    }
}
