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
        Schema::create('admin', function (Blueprint $table) {
            $table->id('admin_id'); 
            $table->string('admin_username')->unique()->notNullable();
            $table->string('admin_email')->unique()->notNullable();
            $table->string('admin_password')->notNullable();
            $table->timestamps();

            $table->index(['admin_username', 'admin_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
