<?php

namespace App\Http\Controllers;
use App\Models\About;
use App\Models\Testimoni;
use App\Models\Distribution;
use App\Models\Brand;
use App\Models\Perusahaan;
use App\Models\BrandCategory;
use App\Models\Produk;
use App\Models\Style;
use App\Models\Manufacture;
use Illuminate\Http\Request;



class MainController extends Controller
{

    public function index()
    {
        $about = About::all();
        $testimoni = Testimoni::all();
        $distribution = Distribution::all();
        $perusahaan = Perusahaan::all();
        $brand = Brand::all();
        $produk = Produk::all();
        $manufacture2 = Manufacture::all();
        $style = Style::all();
        $brand_category = BrandCategory::with('brand', 'kategori')->get();


        // dd($about->mediaawal);
    
        return view("beranda", compact('about', 'testimoni', 'distribution', 'brand', 'perusahaan', 'brand_category', 'produk', 'style', 'manufacture2'));
    }
    
    
    


}
    