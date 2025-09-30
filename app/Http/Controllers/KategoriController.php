<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('kategori.kategori', compact('kategori'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
    try {
        $validatedData = $request->validate([
            'kdktg' => ['required', 'unique:kategori', 'max:20'],
            'nmkategori' => ['required', 'unique:kategori', 'max:50'],
            'status' => ['nullable']
        ], [
            'kdktg.required' => 'Kode kategori harus diisi.',
            'kdktg.unique' => 'Kode kategori sudah terdaftar, gunakan kode lain.',
            'kdktg.max' => 'Kode kategori tidak boleh lebih dari 20 karakter.',
            'nmkategori.required' => 'Nama kategori harus diisi.',
            'nmkategori.unique' => 'Nama kategori sudah terdaftar, gunakan nama lain.',
            'nmkategori.max' => 'Nama kategori tidak boleh lebih dari 50 karakter.'
        ]);

        $kategori = new Kategori();
        $kategori->kdktg = $validatedData['kdktg'];
        $kategori->nmkategori = $validatedData['nmkategori'];

        if ($kategori->save()) {
            notify()->success('Data berhasil ditambahkan!', 'Berhasil');
        } else {
            notify()->error('Data gagal disimpan.', 'Gagal');
        }
    } catch (ValidationException $e) {
        notify()->error('Gagal menambah data. ' . $e->getMessage(), 'Gagal');
        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    return redirect('/dashboard/kategori');
    }   


    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
        {
    try {
        $validatedData = $request->validate([
            'kdktg' => ['required', 'unique:kategori,kdktg,' . $id, 'max:20'],
            'nmkategori' => ['required', 'unique:kategori,nmkategori,' . $id, 'max:50'],
            'status' => ['nullable']
        ], [
            'kdktg.required' => 'Kode kategori harus diisi.',
            'kdktg.unique' => 'Kode kategori sudah terdaftar, gunakan kode lain.',
            'kdktg.max' => 'Kode kategori tidak boleh lebih dari 20 karakter.',
            'nmkategori.required' => 'Nama kategori harus diisi.',
            'nmkategori.unique' => 'Nama kategori sudah terdaftar, gunakan nama lain.',
            'nmkategori.max' => 'Nama kategori tidak boleh lebih dari 50 karakter.'
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->kdktg = $validatedData['kdktg'];
        $kategori->nmkategori = $validatedData['nmkategori'];

        if ($kategori->save()) {
            notify()->success('Data berhasil diperbarui!', 'Berhasil');
        } else {
            notify()->error('Data gagal diperbarui.', 'Gagal');
        }
    } catch (ValidationException $e) {
        notify()->error('Gagal memperbarui data. ' . $e->getMessage(), 'Gagal');
        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    return redirect('/dashboard/kategori');
    }


    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        if ($kategori->delete()) {
            notify()->success('Kategori berhasil dihapus!', 'Berhasil');
        } else {
            notify()->error('Data gagal dihapus.', 'Gagal');
        }

        return redirect('/dashboard/kategori');
    }
}
