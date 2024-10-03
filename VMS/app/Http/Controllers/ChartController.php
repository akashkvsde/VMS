<?php

namespace App\Http\Controllers;

use App\Models\FuelExpense;
use App\Models\OtherFuel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    //

    public function getMonthlyFuelExpenses()
    {
        $currentYear = Carbon::now()->year;
        $monthlyExpenses = FuelExpense::selectRaw('MONTH(filling_date) as month, SUM(filling_amount) as total_amount')
            ->whereYear('filling_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlyExpenses);
    }

    public function getDailyFuelExpenses()
    {
        $today = Carbon::now()->toDateString();
        $dailyExpenses = FuelExpense::selectRaw('DATE(filling_date) as date, SUM(filling_amount) as total_amount')
            ->whereDate('filling_date', $today)
            ->groupBy('date')
            ->get();

        return response()->json($dailyExpenses);
    }

    public function getOtherFuels()
    {
        $otherFuels = OtherFuel::selectRaw('filling_station, SUM(amount) as total_amount')
            ->groupBy('filling_station')
            ->get();

        return response()->json($otherFuels);
    }
}
