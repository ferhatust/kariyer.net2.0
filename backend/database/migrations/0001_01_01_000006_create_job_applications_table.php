<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('basvurular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('is_ilani_id')->constrained('is_ilanlari')->cascadeOnDelete();
            $table->foreignId('kullanici_id')->constrained('kullanicilar')->cascadeOnDelete();
            $table->text('on_yazi')->nullable();
            $table->enum('durum', ['basvuruldu', 'on_secildi', 'reddedildi', 'ise_alindi'])->default('basvuruldu');
            $table->timestamps();

            $table->unique(['is_ilani_id', 'kullanici_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basvurular');
    }
};
