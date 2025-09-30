<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('noart')->unique();
            $table->foreignId('kategori_id')->constrained('kategoris');
            $table->foreignId('brands_id')->constrained('brands');
            $table->string('nmbrg'); 
            $table->string('fotobrg'); 
            $table->json('ukbrg'); 
            $table->text('deskbrg');
            $table->decimal('hrgbrg', 10, 2);
            $table->enum('stokbrg', ['Ready', 'Kosong']);
            $table->string('link_shopee');
            $table->string('link_tokped');
            $table->string('link_ttshop'); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
