<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Force all system tax setups to use tax-inclusive pricing.
 * When a product price is entered, it is treated as including tax.
 * Tax is extracted from the price (not added on top) in reports.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_tax_setups')->update(['is_included' => 1]);
    }

    public function down(): void
    {
        DB::table('system_tax_setups')->update(['is_included' => 0]);
    }
};
