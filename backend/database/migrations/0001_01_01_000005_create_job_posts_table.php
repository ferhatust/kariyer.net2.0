<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('is_ilanlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isveren_id')->constrained('isverenler')->cascadeOnDelete();
            $table->string('sirket_adi');
            $table->string('baslik');
            $table->text('aciklama');
            $table->string('konum')->nullable();
            $table->boolean('uzaktan_mi')->default(false);
            $table->decimal('maas', 12, 2)->nullable();
            $table->string('para_birimi', 8)->nullable();
            $table->string('deneyim_duzeyi')->nullable();
            $table->date('son_tarih')->nullable();
            $table->timestamps();
            $table->index(['isveren_id', 'son_tarih']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('is_ilanlari');
    }
};
