<?php

namespace App\Imports;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProdukImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $kategori = Kategori::where('kdktg', $row['kdktg'])->first();
        if (!$kategori) {
            throw new \Exception("Kategori dengan kode '{$row['kdktg']}' tidak ditemukan.");
        }

        $brand = Brand::where('namabrand', $row['namabrand'])->first();
        if (!$brand) {
            throw new \Exception("Brand dengan nama '{$row['namabrand']}' tidak ditemukan.");
        }

        return new Produk([
            'noart' => $row['noart'],
            'kategori_id' => $kategori->id, // ID kategori yang ditemukan
            'brands_id' => $brand->id, // ID brand yang ditemukan
            'nmbrg' => $row['nmbrg'],
            'ukbrg' => $row['ukbrg'],
            'deskbrg' => $row['deskbrg'],
            'hrgbrg' => $row['hrgbrg'],
            'stokbrg' => $row['stokbrg'],
            'link_shopee' => $row['link_shopee'] ?? null,
            'link_tokped' => $row['link_tokped'] ?? null,
            'link_ttshop' => $row['link_ttshop'] ?? null,
            'fotobrg' => json_encode([]), // Default empty array for photos
        ]);
    }

    public function rules(): array
    {
        return [
            'noart' => 'required|unique:produk,noart',
            'kdktg' => 'required|exists:kategori,kdktg', // Validasi terhadap kode kategori
            'namabrand' => 'required|exists:brand,namabrand', // Validasi terhadap nama brand
            'nmbrg' => 'required',
            'ukbrg' => 'required',
            'deskbrg' => 'required',
            'hrgbrg' => 'required|numeric',
            'stokbrg' => 'required',
            'link_shopee' => 'nullable|url',
            'link_tokped' => 'nullable|url',
            'link_ttshop' => 'nullable|url',
        ];
    }
}
