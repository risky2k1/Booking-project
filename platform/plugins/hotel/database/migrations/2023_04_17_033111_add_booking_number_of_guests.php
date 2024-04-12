<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('ht_bookings', function (Blueprint $table) {
            $table->integer('number_of_guests')->nullable()->after('arrival_time');
        });
    }

    public function down(): void
    {
        Schema::table('ht_bookings', function (Blueprint $table) {
            $table->dropColumn('number_of_guests');
        });
    }
};
