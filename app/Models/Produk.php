<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'trStock';
    public $timestamps = false;
    protected $primaryKey = 'ID'; 
    protected $fillable = [
        'fotobrg', 
    ];

}
