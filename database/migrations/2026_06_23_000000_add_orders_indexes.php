<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('created_at', 'orders_created_at_index');
            $table->index('order_status', 'orders_order_status_index');
            $table->index('payment_status', 'orders_payment_status_index');
            $table->index('seller_is', 'orders_seller_is_index');
            $table->index('checked', 'orders_checked_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_created_at_index');
            $table->dropIndex('orders_order_status_index');
            $table->dropIndex('orders_payment_status_index');
            $table->dropIndex('orders_seller_is_index');
            $table->dropIndex('orders_checked_index');
        });
    }
};
