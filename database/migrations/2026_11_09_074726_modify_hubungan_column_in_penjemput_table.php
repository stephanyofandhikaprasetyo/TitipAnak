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
        Schema::create('penjemput', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anak')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->string('hubungan', 50); // Menggunakan string(50) agar muat untuk 'saudara', 'tetangga', dll.
            $table->string('nik', 16);
            $table->string('no_hp', 20);
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjemput');
    }
};