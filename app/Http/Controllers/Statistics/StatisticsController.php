<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class StatisticsController extends Controller
{
    use ResponseHelper;

    private StatisticsService $_statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->_statisticsService = $statisticsService;
    }

    public function get_statistics():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_statisticsService->get_statistics();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
