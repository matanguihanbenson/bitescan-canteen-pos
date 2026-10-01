<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getMenuInsights()
    {
        try {
            $today = Carbon::today();

            $insights = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.created_at', '>=', $today)
                ->where('transaction_items.order_type', 'menu')
                ->select(
                    DB::raw('COUNT(DISTINCT transaction_items.menu_code) as unique_items_sold'),
                    DB::raw('SUM(transaction_items.quantity) as total_items_sold'),
                    DB::raw('SUM(transaction_items.subtotal) as total_revenue')
                )
                ->first();

            $topItems = DB::table('transaction_items')
                ->where('order_type', 'menu')
                ->select(
                    'menu_name',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(subtotal) as total_sales')
                )
                ->groupBy('menu_name')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'insights' => $insights,
                'top_items' => $topItems
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching menu insights'], 500);
        }
    }

    public function getSalesMetrics()
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::today()->startOfMonth();

            $metrics = [
                'today' => DB::table('transactions')
                    ->where('created_at', '>=', $today)
                    ->where('status', 'completed')
                    ->select(
                        DB::raw('COUNT(*) as count'),
                        DB::raw('SUM(total_amount) as total'),
                        DB::raw('AVG(total_amount) as average')
                    )
                    ->first(),

                'month' => DB::table('transactions')
                    ->where('created_at', '>=', $thisMonth)
                    ->where('status', 'completed')
                    ->select(
                        DB::raw('COUNT(*) as count'),
                        DB::raw('SUM(total_amount) as total')
                    )
                    ->first(),

                'pending' => DB::table('transactions')
                    ->where('status', 'pending')
                    ->count()
            ];

            return response()->json($metrics);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching sales metrics'], 500);
        }
    }
}
