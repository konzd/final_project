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
        Schema::create('admins', function (Blueprint $table) { // Ubah jadi 'admins' sesuai konvensi Laravel
            $table->id(); // Laravel otomatis membuat kolom 'id' sebagai primary key
            $table->string('admin_username')->unique();
            $table->string('admin_email')->unique();
            $table->string('admin_password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins'); // Pastikan nama tabel sesuai
    }
};
