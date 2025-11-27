<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Finance;
use Carbon\Carbon;

class ReportController extends Controller {
    public function index(Request $request) {
        // Logika report filter berdasarkan tanggal
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        $sales = Transaction::whereBetween('created_at', [$startDate, $endDate])->get();
        $finances = Finance::whereBetween('transaction_date', [$startDate, $endDate])->get();

        return view('reports.index', compact('sales', 'finances', 'startDate', 'endDate'));
    }
}