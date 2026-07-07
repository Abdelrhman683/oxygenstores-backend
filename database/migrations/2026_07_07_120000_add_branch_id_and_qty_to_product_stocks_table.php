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
        Schema::table('product_stocks', function (Blueprint $table) {
            // Add branch_id column (nullable so existing records stay valid)
            if (!Schema::hasColumn('product_stocks', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('product_id');
            }
            // Add qty column for per-branch stock quantity
            if (!Schema::hasColumn('product_stocks', 'qty')) {
                $table->unsignedInteger('qty')->default(0)->after('branch_id');
            }
        });

        // Add indexes separately after columns exist
        Schema::table('product_stocks', function (Blueprint $table) {
            // Add index on branch_id for faster lookups
            $table->index('branch_id', 'idx_product_stocks_branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('idx_product_stocks_branch_id');
            // Drop columns
            $table->dropColumn(['branch_id', 'qty']);
        });
    }
};
