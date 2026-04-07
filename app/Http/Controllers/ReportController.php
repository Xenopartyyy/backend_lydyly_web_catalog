<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ReportController extends Controller
{
    // GET /api/dashboard/report/gudang
    public function getGudang()
    {
        try {
            $gudang = DB::connection('mssql')
                ->select("SELECT namagdg, kodegdg, [DEFAULT] FROM gdg ORDER BY namagdg");

            return response()->json([
                'success' => true,
                'data'    => $gudang,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil data gudang',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/dashboard/report/penjualan-periode?warehouse=LYDYLY&year=2026
    public function penjualanPeriode(Request $request)
    {
        try {
            $warehouse = $request->query('warehouse');
            $year      = $request->query('year', date('Y'));

            if (!$warehouse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter warehouse wajib diisi',
                ], 422);
            }

            $data = DB::connection('mssql')
                ->select("EXEC GetPurchaseEstimate_sell @warehouse = ?, @year = ?", [
                    $warehouse,
                    (int) $year,
                ]);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil data penjualan per periode',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}