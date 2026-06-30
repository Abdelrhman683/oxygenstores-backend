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
        Schema::table('orders', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders MODIFY customer_id BIGINT UNSIGNED NULL');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders MODIFY delivery_man_id BIGINT UNSIGNED NULL');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders MODIFY seller_id BIGINT UNSIGNED NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders MODIFY customer_id VARCHAR(15) NULL');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders MODIFY delivery_man_id BIGINT NULL');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders MODIFY seller_id BIGINT NULL');
        });
    }
};
