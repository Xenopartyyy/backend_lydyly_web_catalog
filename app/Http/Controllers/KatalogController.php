<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Perusahaan;
use App\Models\Manufacture;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    $noart = $request->input('noart');
    $search = $request->input('search');
    $kategori_id = $request->input('kategori');
    $brands_id = $request->input('brand');
    $stok = $request->input('stokbrg');
    $style = $request->input('stylebrg');
    $harga_min = $request->input('harga_min');
    $harga_max = $request->input('harga_max');
    $sort = $request->input('sort');

    $query = Produk::query();

    if ($noart) {
        $query->where('noart', 'like', '%' . $noart . '%');
    }

    if ($search) {
        $search = str_replace(' ', '', $search);
        $query->whereRaw("REPLACE(nmbrg, ' ', '') LIKE ?", ['%' . $search . '%']);
    }

    if ($kategori_id) {
        $query->where('kategori_id', $kategori_id);
    }

    if ($brands_id) {
        $query->where('brands_id', $brands_id);
    }

    if ($harga_min) {
        $query->where('hrgbrg', '>=', $harga_min);
    }

    if ($harga_max) {
        $query->where('hrgbrg', '<=', $harga_max);
    }

    if ($stok) {
        if ($stok === 'Ready') {
            $query->where('stokbrg', 'Ready'); 
        } elseif ($stok === 'Kosong') {
            $query->where('stokbrg', 'Kosong'); 
        }
    }

    if ($style) {
        if ($style === 'AGREE FIT') {
            $query->where('stylebrg', 'AGREE FIT'); 
        } elseif ($style === 'AGREE SPORT') {
            $query->where('stylebrg', 'AGREE SPORT'); 
        }
    }

    if ($sort === 'harga_termahal') {
        $query->orderBy('hrgbrg', 'desc'); 
    } elseif ($sort === 'harga_termurah') {
        $query->orderBy('hrgbrg', 'asc'); 
    } elseif ($sort === 'promo_terbesar') {
        $query->where('hrgbrgfake', '>', 0)->orderByRaw('(hrgbrgfake - hrgbrg) DESC'); 
    }

    $produk = $query->paginate(16)->withQueryString();

    // Filter untuk kategori dan brand saling terkait
    $brand = Brand::query();
    $kategori = Kategori::query();

    if ($kategori_id) {
        // Tampilkan brand sesuai kategori yang dipilih
        $brand_ids = BrandCategory::where('kategori_id', $kategori_id)->pluck('brands_id');
        $brand->whereIn('id', $brand_ids);
    }

    if ($brands_id) {
        // Tampilkan kategori sesuai brand yang dipilih
        $kategori_ids = BrandCategory::where('brands_id', $brands_id)->pluck('kategori_id');
        $kategori->whereIn('id', $kategori_ids);
    }

    $brand = $brand->get();
    $kategori = $kategori->get();
    $perusahaan = Perusahaan::all();
    $brand_category = BrandCategory::all();
    $manufacture2 = Manufacture::all();
    $about = About::all();

    return view('katalog', compact('brand', 'kategori', 'produk', 'perusahaan', 'brand_category', 'manufacture2', 'about'));
}

}