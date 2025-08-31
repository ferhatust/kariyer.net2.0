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
        // İşverenler
        Schema::create('isverenler', function (Blueprint $table) {
            $table->id();
            $table->string('sirket_adi');
            $table->string('e_posta')->unique();
            $table->string('sifre');
            $table->string('konum')->nullable();
            $table->string('telefon')->nullable();
            $table->text('hakkinda')->nullable();
            $table->timestamps();
        });

        // Kullanıcılar (Adaylar)
        Schema::create('kullanicilar', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->string('soyad');
            $table->string('e_posta')->unique();
            $table->string('sifre');
            $table->string('eski_calistigi_yer')->nullable();
            $table->string('telefon')->nullable();
            $table->string('okudugu_egitim')->nullable();
            $table->string('deneyim_duzeyi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kullanicilar');
        Schema::dropIfExists('isverenler');
    }
};
