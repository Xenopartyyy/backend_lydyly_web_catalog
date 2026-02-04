<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class ProdukController extends Controller
{
    
    public function index(Request $request)
{
    try {
        $warehouse = $request->get('warehouse', 'GDG101');
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $cek = $request->get('cek', 1);

        // ✅ PANGGIL SP BARU (1 query aja!)
        $produk = DB::select('EXEC GetProdukWithStock ?, ?, ?', [
            $warehouse,
            $tanggal,
            $cek
        ]);

        // Format data (minimal processing)
        $produk = collect($produk)->map(function ($row) {
            // Cast ke float
            $row->StockAkhir = (float) ($row->StockAkhir ?? 0);
            $row->StockCME = (float) ($row->StockCME ?? 0);
            $row->HargaBeli = (float) ($row->HargaBeli ?? 0);
            $row->HargaJual = (float) ($row->HargaJual ?? 0);
            $row->HargaJual2 = (float) ($row->HargaJual2 ?? 0);
            $row->HargaJual3 = (float) ($row->HargaJual3 ?? 0);
            $row->HargaJual4 = (float) ($row->HargaJual4 ?? 0);
            $row->HargaJual5 = (float) ($row->HargaJual5 ?? 0);
            $row->YUAN = (float) ($row->YUAN ?? 0);

            // Limit deskripsi
            $row->deskbrg = $row->deskbrg ? Str::limit($row->deskbrg, 50) : '-';

            // Parse foto
            if (!empty($row->fotobrg)) {
                $images = json_decode($row->fotobrg, true);
                $row->fotobrg = (json_last_error() === JSON_ERROR_NONE && is_array($images)) 
                    ? array_map(fn($p) => ltrim(str_replace('\\', '', trim($p)), '/'), $images)
                    : [];
            } else {
                $row->fotobrg = [];
            }

            return $row;
        });

        return response()->json([
            'success' => true,
            'count' => $produk->count(),
            'data' => $produk,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

    } catch (\Exception $e) {
        Log::error("Error ProdukController@index: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil data produk',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function show($id)
    {
        try {
            // 1. Gunakan findOrFail untuk efisiensi dan error handling otomatis
            $produk = Produk::select([
                'id',
                'barcode',
                'ArtNo',
                'NamaStock',
                'Jenis',
                'Merek',
                'deskbrg',
                'fotobrg',
                'HargaJual',
                'HargaJual2',
                'HargaJual3',
                'HargaJual4',
                'HargaJual5',
                'YUAN',
                'HargaBeli'
            ])->findOrFail($id);

            // 2. Optimasi processing gambar dengan early return
            $images = [];
            if ($produk->fotobrg) {
                $fotobrg = json_decode($produk->fotobrg, true);
                if (is_array($fotobrg) && !empty($fotobrg)) {
                    foreach ($fotobrg as $foto) {
                        // Langsung format tanpa pengecekan berulang
                        $cleanPath = str_replace(['\\', '/'], '/', $foto);
                        $images[] = str_starts_with($cleanPath, 'produk/')
                            ? $cleanPath
                            : 'produk/' . basename($cleanPath);
                    }
                }
            }

            // 3. Optimasi stock query dengan parameter binding yang lebih efisien
            $barcode = trim($produk->barcode);
            $stockAkhir = 0;

            if ($barcode) {

                $stockData = DB::select('EXEC GetItemStockBarcode_New ?, ?, ?, ?', [
                    'GDG101',
                    1,
                    now()->format('Y-m-d'),
                    1
                ]);
                $stockCollection = collect($stockData)->keyBy('barcode');
                $stockItem = $stockCollection->get($barcode);
                $stockAkhir = $stockItem ? $stockItem->{'Stock Akhir'} : 0;
            }

            $produk->StockAkhir = $stockAkhir;

            return response()->json([
                'success' => true,
                'data' => [
                    'produk' => $produk,
                    'images' => $images
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Product show error: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'success' => false,
                'message' => 'Gagal mengambil detail produk'
            ], 500);
        }
    }

        // public function index(Request $request)
    // {
    //     try {
    //         $warehouse = $request->get('warehouse', 'GDG101');
    //         $aktif = 1;
    //         $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
    //         $cek = $request->get('cek', 1);

    //         // Ambil stok dari stored procedure
    //         $stockData = DB::select('EXEC GetItemStockBarcode_New ?, ?, ?, ?', [
    //             $warehouse,
    //             $aktif,
    //             $tanggal,
    //             $cek
    //         ]);

    //         $stockCollection = collect($stockData)->keyBy('barcode')->map(fn($item) => $item->{'Stock Akhir'} ?? 0);

    //         // Ambil produk aktif
    //         $produk = Produk::select([
    //             'id',
    //             'barcode',
    //             'ArtNo',
    //             'NamaStock',
    //             'Jenis',
    //             'Merek',
    //             'deskbrg',
    //             'fotobrg',
    //             'HargaJual5',
    //             'YUAN',
    //             'HargaBeli'
    //         ])->where('Aktif', 1)->get();

    //         // Format data
    //         $produk = $produk->map(function ($row) use ($stockCollection) {
    //             $row->StockAkhir = (float) $stockCollection->get(trim($row->barcode), 0);
    //             $row->HargaBeli   = $row->HargaBeli   ? (float) $row->HargaBeli   : 0;
    //             $row->HargaJual5  = $row->HargaJual5  ? (float) $row->HargaJual5  : 0;
    //             $row->YUAN        = $row->YUAN        ? (float) $row->YUAN        : 0;
    //             $row->deskbrg     = $row->deskbrg ? Str::limit($row->deskbrg, 50) : '-';

    //             // Foto barang
    //             if (!empty($row->fotobrg)) {
    //                 $images = json_decode($row->fotobrg, true);
    //                 if (json_last_error() === JSON_ERROR_NONE && is_array($images)) {
    //                     $row->fotobrg = array_map(fn($path) => ltrim(str_replace('\\', '', trim($path)), '/'), $images);
    //                 } else {
    //                     $row->fotobrg = [];
    //                 }
    //             } else {
    //                 $row->fotobrg = [];
    //             }

    //             return $row;
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'count'   => $produk->count(),
    //             'data'    => $produk,
    //         ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    //     } catch (\Exception $e) {
    //         Log::error("Error ProdukController@index: " . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat mengambil data produk',
    //             'error'   => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function update(Request $request, $ID)
    {
        try {
            $validatedData = $request->validate([
                'fotobrg.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,jfif',
                'deleted_images' => 'nullable|array',
                'deleted_images.*' => 'string',
            ]);

            $produk = Produk::findOrFail($ID);
            $existingImages = $produk->fotobrg ? json_decode($produk->fotobrg, true) : [];

            // Hapus gambar yang dipilih untuk dihapus
            if ($request->has('deleted_images')) {
                foreach ($request->deleted_images as $deletedPath) {
                    // Decode JSON escape kalau ada
                    $cleanPath = str_replace(['\\"', '\\/'], ['', '/'], $deletedPath);
                    $cleanPath = trim($cleanPath, '"'); // buang kutip ekstra kalau ada

                    // Ambil hanya "produk/xxx.jpg"
                    $filename = basename($cleanPath);
                    $cleanPath = "produk/" . $filename;

                    // Hapus fisik file
                    if (Storage::disk('public')->exists($cleanPath)) {
                        Storage::disk('public')->delete($cleanPath);
                    }

                    // Hapus dari array database
                    $existingImages = array_filter($existingImages, function ($item) use ($cleanPath) {
                        return $item !== $cleanPath;
                    });
                }
                $existingImages = array_values($existingImages);
            }


            // Upload gambar baru
            if ($request->hasFile('fotobrg')) {
                foreach ($request->file('fotobrg') as $file) {
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($file);

                    // Kalau width lebih dari 1280, resize proporsional
                    if ($image->width() > 1280) {
                        $image->scaleDown(1280); // Hanya resize jika lebih besar dari 1280
                    }

                    // Simpan ke JPEG kualitas 80%
                    $compressedImage = $image->toJpeg(80);

                    $filename = uniqid() . '.jpg';
                    $path = 'produk/' . $filename;

                    Storage::disk('public')->put($path, $compressedImage);
                    $existingImages[] = $path;
                }
            }


            // Update database
            $produk->fotobrg = !empty($existingImages)
                ? json_encode($existingImages, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)
                : null;


            if ($produk->save()) {
                // Return updated images as full URLs
                $imageUrls = array_map(function ($path) {
                    return url('storage/' . $path);
                }, $existingImages);

                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil diperbarui',
                    'data' => $produk,
                    'images' => $imageUrls
                ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui produk'
                ], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
