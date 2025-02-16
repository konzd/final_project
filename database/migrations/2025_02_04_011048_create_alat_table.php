<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id('alat_id'); 
            $table->foreignId('alat_kategori_id')->constrained('kategori', 'kategori_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('alat_nama', 150); 
            $table->string('alat_deskripsi', 255)->nullable(); 
            $table->integer('alat_hargaperhari'); 
            $table->integer('alat_stok'); 
            $table->string('alat_gambar')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
