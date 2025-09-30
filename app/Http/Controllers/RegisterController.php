<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Menampilkan form register
    public function showRegisterForm()
    {
        return view('register'); // Mengacu pada file register.blade.php
    }

    // Menyimpan data registrasi
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'password' => 'required|string|min:8|confirmed', // Konfirmasi password
        ]);

        // Simpan data ke database
        User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password), // Hash password sebelum disimpan
        ]);

        // Redirect ke halaman login atau dashboard
        return redirect('/login/akun/lydyly2')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
