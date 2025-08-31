<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('isverenler', function (Blueprint $table) {
            $table->string('sektor')->nullable()->after('konum');
            $table->string('website')->nullable()->after('sektor');
        });
    }

    public function down(): void
    {
        Schema::table('isverenler', function (Blueprint $table) {
            $table->dropColumn(['sektor', 'website']);
        });
    }
};
