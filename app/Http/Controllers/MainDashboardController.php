<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;


class MainDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            // Total kategori
            $kategori = Kategori::count(); 

            // Produk per jenis (group by Jenis)
            $categories = Produk::select('Jenis')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('Jenis')
                ->orderBy('Jenis')
                ->get();

            // Total produk
            $produk = Produk::count();

            // Produk aktif
            $produkaktif = Produk::where('aktif', 1)->count();

            // Produk nonaktif
            $produknonaktif = Produk::where('aktif', 0)->count();

            return response()->json([
                'success' => true, 
                'total_kategori' => $kategori, 
                'total_produk' => $produk, 
                'produk_aktif' => $produkaktif, 
                'produk_nonaktif' => $produknonaktif, 
                'categories' => $categories
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan saat mengambil data', 
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
