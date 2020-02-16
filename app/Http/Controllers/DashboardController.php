<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Webhook;
use App\Models\Bot;
use App\Models\PayloadHistory;
use App\Models\MessageHistory;
use Illuminate\Http\Request;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Enums\PayloadHistoryStatus;
use App\Enums\MessageHistoryStatus;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $statisticParams = $this->handleStatisticParam($request->get('daterange'));

        $period = $this->getDateFromRange($statisticParams['fromDate'], $statisticParams['toDate']);
        $payloadFailded = PayloadHistory::dataChartByUser($statisticParams, PayloadHistoryStatus::FAILED, Auth::id());
        $payloadSuccess = PayloadHistory::dataChartByUser($statisticParams, PayloadHistoryStatus::SUCCESS, Auth::id());
        $messageFailed = MessageHistory::dataChartByUser($statisticParams, MessageHistoryStatus::FAILED, Auth::id());
        $messageSuccess = MessageHistory::dataChartByUser($statisticParams, MessageHistoryStatus::SUCCESS, Auth::id());

        $payloadFaildedQuantity = $this->getDataChart($payloadFailded, $period);
        $payloadSuccessQuantity = $this->getDataChart($payloadSuccess, $period);
        $messageFaildedQuantity = $this->getDataChart($messageFailed, $period);
        $messageSuccessQuantity = $this->getDataChart($messageSuccess, $period);

        $payloadFailedChart = $this->convertArrayChart($payloadFaildedQuantity, $period);
        $payloadSuccessChart = $this->convertArrayChart($payloadSuccessQuantity, $period);
        $messageFailedChart = $this->convertArrayChart($messageFaildedQuantity, $period);
        $messageSuccessChart = $this->convertArrayChart($messageSuccessQuantity, $period);
        $dateChart = $this->convertArrayChart($period);

        $payloadFaildedCount = array_sum($payloadFaildedQuantity);
        $payloadSuccessCount = array_sum($payloadSuccessQuantity);
        $messageFaildedCount = array_sum($messageFaildedQuantity);
        $messageSuccessCount = array_sum($messageSuccessQuantity);

        $countData = [
            'webhook' => Webhook::byUser(Auth::id())->count(),
            'payloadHistory' => ($payloadFaildedCount + $payloadSuccessCount),
            'messageHistory' => ($messageFaildedCount + $messageSuccessCount),
            'bot' => Bot::byUser(Auth::id())->count(),
        ];
        $payloadChartData = [
            'failedCases' => $payloadFaildedCount,
            'successCases' => $payloadSuccessCount,
            'dateChart' => $dateChart,
            'payloadFailedChart' => $payloadFailedChart,
            'payloadSuccessChart' => $payloadSuccessChart,
        ];
        $messageChartData = [
            'failedCases' => $messageFaildedCount,
            'successCases' => $messageSuccessCount,
            'dateChart' => $dateChart,
            'messageFailedChart' => $messageFailedChart,
            'messageSuccessChart' => $messageSuccessChart,
        ];
        return view(
            'dashboard',
            [
                'countData' => $countData,
                'payloadHistory' => $payloadChartData,
                'messageHistory' => $messageChartData,
            ]
        );
    }

    private function handleStatisticParam($dateRange)
    {
        if ($dateRange == null) {
            $statisticParams = ['fromDate' => date('01-m-Y'), 'toDate' => date('d-m-Y')];
        } else {
            $dateRangeParams = explode(' - ', $dateRange, 2);
            $statisticParams = ['fromDate' => $dateRangeParams[0], 'toDate' => $dateRangeParams[1]];
        }

        if ($statisticParams['fromDate'] == null) {
            $statisticParams['fromDate'] = date('01-m-Y');
        }

        if ($statisticParams['toDate'] == null) {
            $statisticParams['toDate'] = date('d-m-Y');
        }
        return $statisticParams;
    }

    private function getDateFromRange($start, $end, $format = 'd-m-Y')
    {
        $array = [];
        $interval = new DateInterval('P1D');
        $realEnd = DateTime::createFromFormat('d-m-Y', $end);
        $realEnd->add($interval);
        $period = new DatePeriod(DateTime::createFromFormat('d-m-Y', $start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }
        return $array;
    }

    private function convertArrayChart($array)
    {
        $arrayChart = [];

        foreach ($array as $key => $value) {
            $arrayChart[] = [$key, $value];
        }
        return $arrayChart;
    }

    private function getDataChart($data, $period)
    {
        $dataChart = [];

        foreach ($period as $date) {
            if (array_key_exists($date, $data->toArray())) {
                $dataChart[] = $data[$date];
            } else {
                $dataChart[] = 0;
            }
        }
        return $dataChart;
    }
}
