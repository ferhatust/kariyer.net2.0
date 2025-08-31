<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('isverenler', function (Blueprint $table) {
            $table->string('api_token', 80)->nullable()->unique()->after('hakkinda');
        });
    }

    public function down(): void
    {
        Schema::table('isverenler', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }
};
