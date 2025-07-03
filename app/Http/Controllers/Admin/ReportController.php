<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use PDF;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query();

        if ($request->start_date && $request->end_date) {
            $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $orders = $orders->orderBy('created_at', 'desc')->get();
        $total = $orders->sum('billing_total');

        return view('admin.reports.index', compact('orders', 'total'));
    }

    public function exportPdf(Request $request)
    {
        $orders = Order::query();

        if ($request->start_date && $request->end_date) {
            $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $orders = $orders->orderBy('created_at', 'desc')->get();
        $total = $orders->sum('billing_total');

        $pdf = PDF::loadView('admin.reports.pdf', compact('orders', 'total', 'request'));
        return $pdf->download('laporan_penjualan.pdf');
    }
}
