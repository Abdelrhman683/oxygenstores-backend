<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MigrateFromWordPress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wp:migrate
                            {--step= : Run a specific step only (categories|sellers|customers|products|orders|branches)}
                            {--dry-run : Preview the data without inserting}
                            {--limit=0 : Limit number of records per step (0 = all)}
                            {--skip-existing : Skip records that already exist}
                            {--fresh : Truncate existing data in target tables before migrating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from WordPress/WooCommerce/Dokan to Laravel 6Valley';

    private bool $dryRun = false;
    private int $limit = 0;
    private bool $skipExisting = false;
    private bool $fresh = false;

    /**
     * Tables to truncate per step (order matters: children before parents).
     */
    private array $stepTables = [
        'categories' => ['categories'],
        'brands' => ['brands'],
        'sellers' => ['shops', 'sellers'],
        'branches' => ['branchmeta', 'branch_product', 'branches'],
        'customers' => ['users'],
        'products' => ['product_stocks', 'order_details', 'branch_product', 'products'],
        'orders' => ['order_details', 'orders'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->dryRun = $this->option('dry-run');
        $this->limit = (int) $this->option('limit');
        $this->skipExisting = $this->option('skip-existing');
        $this->fresh = $this->option('fresh');

        if ($this->dryRun) {
            $this->warn('🔍 DRY RUN MODE - No data will be written to the database');
        }

        // Verify WordPress connection is available
        try {
            DB::connection('mysql_wordpress')->getPdo();
        } catch (\Exception $e) {
            $this->error('❌ Cannot connect to WordPress database (mysql_wordpress).');
            $this->error('   Make sure the database "' . config('database.connections.mysql_wordpress.database') . '" exists and is imported.');
            $this->error('   Error: ' . $e->getMessage());
            return self::FAILURE;
        }

        $step = $this->option('step');

        if ($step) {
            // Fresh for a single step
            if ($this->fresh && !$this->dryRun) {
                $this->truncateStep($step);
            }
            $this->runStep($step);
        } else {
            $this->info('🚀 Starting Full WordPress → Laravel Migration');
            $this->info('============================================================');

            // Fresh: truncate ALL tables at once before starting
            if ($this->fresh && !$this->dryRun) {
                $this->truncateAll();
            }

            $this->runStep('categories');
            $this->runStep('brands');
            $this->runStep('sellers');
            $this->runStep('branches');
            $this->runStep('customers');
            $this->runStep('products');
            $this->runStep('orders');
        }

        $this->info('');
        $this->info('✅ Migration completed!');
        return self::SUCCESS;
    }

    // =========================================================================
    // TRUNCATE HELPERS
    // =========================================================================

    /**
     * Truncate all tables for a full fresh migration.
     */
    private function truncateAll(): void
    {
        // Reverse order so foreign-key children are cleared first
        $allTables = [
            'translations',
            'order_details',
            'orders',
            'product_stocks',
            'branch_product',
            'products',
            'users',
            'branchmeta',
            'branches',
            'shops',
            'sellers',
            'brands',
            'categories',
        ];

        $this->warn('⚠️  --fresh: Truncating existing data...');

        if (!$this->confirm('This will delete all data from the listed tables. Are you sure?', false)) {
            $this->error('Cancelled.');
            exit(self::FAILURE);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($allTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("   🗑  Truncated: {$table}");
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('   ✅ Tables cleared. Starting migration...');
        $this->info('');
    }

    /**
     * Truncate tables for a single step.
     */
    private function truncateStep(string $step): void
    {
        $tables = $this->stepTables[$step] ?? [];

        if (empty($tables)) {
            return;
        }

        $this->warn("⚠️  --fresh: Truncating tables for step [{$step}]: " . implode(', ', $tables));

        if (!$this->confirm('Are you sure?', false)) {
            $this->error('Cancelled.');
            exit(self::FAILURE);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("   🗑  Truncated: {$table}");
            }
        }

        // Delete respective translations
        $typeMap = [
            'categories' => 'App\Models\Category',
            'brands'     => 'App\Models\Brand',
            'products'   => 'App\Models\Product',
            'branches'   => 'App\Models\Branch',
        ];
        if (isset($typeMap[$step])) {
            DB::table('translations')->where('translationable_type', $typeMap[$step])->delete();
            $this->line("   🗑  Deleted translations for: {$typeMap[$step]}");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    // =========================================================================
    // STEP DISPATCHER
    // =========================================================================

    private function runStep(string $step): void
    {
        match ($step) {
            'categories' => $this->migrateCategories(),
            'brands' => $this->migrateBrands(),
            'sellers' => $this->migrateSellers(),
            'branches' => $this->migrateBranches(),
            'customers' => $this->migrateCustomers(),
            'products' => $this->migrateProducts(),
            'orders' => $this->migrateOrders(),
            default => $this->error("Unknown step: $step"),
        };
    }

    // =========================================================================
    // STEP 1: CATEGORIES
    // =========================================================================

    private function migrateCategories(): void
    {
        $this->info('');
        $this->info('📁 Step 1: Migrating Categories...');

        // Get all product categories from WordPress
        $wpCategories = DB::connection('mysql_wordpress')
            ->table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->leftJoin('wp_termmeta', function ($join) {
                $join->on('wp_terms.term_id', '=', 'wp_termmeta.term_id')
                    ->where('wp_termmeta.meta_key', '=', 'thumbnail_id');
            })
            ->where('wp_term_taxonomy.taxonomy', 'product_cat')
            ->where('wp_terms.name', '!=', 'Uncategorized')
            ->select([
                'wp_terms.term_id',
                'wp_terms.name',
                'wp_terms.slug',
                'wp_term_taxonomy.parent',
                'wp_term_taxonomy.count',
                'wp_termmeta.meta_value as thumbnail_attachment_id',
            ])
            ->orderBy('wp_term_taxonomy.parent')
            ->when($this->limit > 0, fn($q) => $q->limit($this->limit))
            ->get();

        $this->info("   Found {$wpCategories->count()} categories in WordPress.");

        if ($this->dryRun) {
            $this->table(
                ['WP ID', 'Name', 'Slug', 'Parent WP ID', 'Product Count'],
                $wpCategories->map(fn($c) => [
                    $c->term_id,
                    $c->name,
                    $c->slug,
                    $c->parent,
                    $c->count
                ])->toArray()
            );
            return;
        }

        // Map WP term_id → Laravel category id
        $wpToLaravelCategoryMap = [];

        // First pass: get existing mapping if skip-existing
        if ($this->skipExisting) {
            $existing = DB::table('categories')
                ->whereNotNull('slug')
                ->pluck('id', 'slug')
                ->toArray();
        }

        $bar = $this->output->createProgressBar($wpCategories->count());
        $bar->start();

        $inserted = 0;
        $skipped = 0;

        // Two-pass: first insert top-level (parent=0), then children
        $sorted = $wpCategories->sortBy(fn($c) => $c->parent === 0 ? 0 : 1);

        foreach ($sorted as $cat) {
            $bar->advance();

            if ($this->skipExisting && isset($existing[$cat->slug])) {
                $wpToLaravelCategoryMap[$cat->term_id] = $existing[$cat->slug];
                $skipped++;
                continue;
            }

            // Get thumbnail image URL if exists
            $imageUrl = null;
            if ($cat->thumbnail_attachment_id) {
                $attachment = DB::connection('mysql_wordpress')
                    ->table('wp_posts')
                    ->where('ID', $cat->thumbnail_attachment_id)
                    ->value('guid');
                if ($attachment) {
                    $imageUrl = $this->extractWordPressFilename($attachment);
                }
            }

            $parentLaravelId = 0;
            if ($cat->parent > 0 && isset($wpToLaravelCategoryMap[$cat->parent])) {
                $parentLaravelId = $wpToLaravelCategoryMap[$cat->parent];
            }

            $fixedName = $this->fixMojibake($cat->name);

            $laravelId = DB::table('categories')->insertGetId([
                'name' => $fixedName,
                'slug' => Str::slug($this->fixMojibake(urldecode($cat->slug))) ?: Str::slug($fixedName),
                'icon' => $imageUrl,
                'parent_id' => $parentLaravelId,
                'position' => 1,
                'home_status' => 0,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add translations for categories name
            $this->updateOrCreateTranslation('App\Models\Category', $laravelId, 'ar', 'name', $fixedName);
            $this->updateOrCreateTranslation('App\Models\Category', $laravelId, 'en', 'name', $fixedName);

            $wpToLaravelCategoryMap[$cat->term_id] = $laravelId;
            $inserted++;
        }

        $bar->finish();
        $this->info('');
        $this->info("   ✅ Categories: {$inserted} inserted, {$skipped} skipped.");

        // Store the map for later use
        cache(['wp_category_map' => $wpToLaravelCategoryMap], now()->addHours(2));
    }

    // =========================================================================
    // STEP 2: BRANDS
    // =========================================================================

    private function migrateBrands(): void
    {
        $this->info('');
        $this->info('🏷️  Step 2: Migrating Brands...');

        // Brands in WooCommerce are stored as product attributes (pa_brand taxonomy)
        $wpBrands = DB::connection('mysql_wordpress')
            ->table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->where('wp_term_taxonomy.taxonomy', 'pa_brand')
            ->select([
                'wp_terms.term_id',
                'wp_terms.name',
                'wp_terms.slug',
                'wp_term_taxonomy.count',
            ])
            ->when($this->limit > 0, fn($q) => $q->limit($this->limit))
            ->get();

        $this->info("   Found {$wpBrands->count()} brands in WordPress.");

        if ($this->dryRun) {
            $this->table(
                ['WP ID', 'Name', 'Slug', 'Product Count'],
                $wpBrands->map(fn($b) => [$b->term_id, $b->name, $b->slug, $b->count])->toArray()
            );
            return;
        }

        $wpToLaravelBrandMap = [];
        $inserted = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($wpBrands->count());
        $bar->start();

        foreach ($wpBrands as $brand) {
            $bar->advance();

            if ($this->skipExisting) {
                $existing = DB::table('brands')->where('slug', Str::slug($brand->slug))->value('id');
                if ($existing) {
                    $wpToLaravelBrandMap[$brand->term_id] = $existing;
                    $skipped++;
                    continue;
                }
            }

            $fixedName = $this->fixMojibake($brand->name);

            $laravelId = DB::table('brands')->insertGetId([
                'name' => $fixedName,
                'slug' => Str::slug($this->fixMojibake(urldecode($brand->slug))) ?: Str::slug($fixedName),
                'image' => '',
                'image_storage_type' => 'public',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add translations for brands name
            $this->updateOrCreateTranslation('App\Models\Brand', $laravelId, 'ar', 'name', $fixedName);
            $this->updateOrCreateTranslation('App\Models\Brand', $laravelId, 'en', 'name', $fixedName);

            $wpToLaravelBrandMap[$brand->term_id] = $laravelId;
            $inserted++;
        }

        $bar->finish();
        $this->info('');
        $this->info("   ✅ Brands: {$inserted} inserted, {$skipped} skipped.");

        cache(['wp_brand_map' => $wpToLaravelBrandMap], now()->addHours(2));
    }

    // =========================================================================
    // STEP 3: SELLERS (Dokan Vendors)
    // =========================================================================

    private function migrateSellers(): void
    {
        $this->info('');
        $this->info('🏪 Step 3: Migrating Sellers (Dokan Vendors)...');

        // Get users with seller/vendor role from WordPress
        $wpSellers = DB::connection('mysql_wordpress')
            ->table('wp_users')
            ->join('wp_usermeta as cap', function ($join) {
                $join->on('wp_users.ID', '=', 'cap.user_id')
                    ->where('cap.meta_key', '=', 'wp_capabilities');
            })
            ->where(function ($q) {
                $q->where('cap.meta_value', 'like', '%seller%')
                    ->orWhere('cap.meta_value', 'like', '%vendor%');
            })
            ->select('wp_users.*')
            ->when($this->limit > 0, fn($q) => $q->limit($this->limit))
            ->get();

        $this->info("   Found {$wpSellers->count()} sellers in WordPress.");

        if ($this->dryRun) {
            $this->table(
                ['WP ID', 'Login', 'Email', 'Display Name', 'Registered'],
                $wpSellers->map(fn($s) => [
                    $s->ID,
                    $s->user_login,
                    $s->user_email,
                    $s->display_name,
                    $s->user_registered
                ])->toArray()
            );
            return;
        }

        $wpToLaravelSellerMap = [];
        $inserted = 0;
        $skipped = 0;
        $processedSellers = [];

        $bar = $this->output->createProgressBar($wpSellers->count());
        $bar->start();

        foreach ($wpSellers as $seller) {
            $bar->advance();

            $email = trim($seller->user_email);
            if (empty($email)) {
                $email = $seller->user_login . '@noselleremail.com';
            }
            $emailLower = strtolower($email);

            if (isset($processedSellers[$emailLower]) || DB::table('sellers')->where('email', $emailLower)->exists()) {
                $existingId = DB::table('sellers')->where('email', $emailLower)->value('id');
                if ($existingId) {
                    $wpToLaravelSellerMap[$seller->ID] = $existingId;
                }
                $skipped++;
                continue;
            }

            $processedSellers[$emailLower] = true;

            // Get seller meta data from Dokan
            $meta = DB::connection('mysql_wordpress')
                ->table('wp_usermeta')
                ->where('user_id', $seller->ID)
                ->whereIn('meta_key', [
                    'first_name',
                    'last_name',
                    'billing_phone',
                    'dokan_profile_settings',
                    'dokan_store_name',
                    'dokan_payment',
                    'billing_address_1',
                    'billing_city',
                    'billing_country',
                ])
                ->pluck('meta_value', 'meta_key')
                ->toArray();

            $dokanProfile = isset($meta['dokan_profile_settings'])
                ? @unserialize($meta['dokan_profile_settings'])
                : [];

            $nameParts = explode(' ', $seller->display_name, 2);
            $firstName = $this->fixMojibake($meta['first_name'] ?? $nameParts[0] ?? $seller->user_login);
            $lastName = $this->fixMojibake($meta['last_name'] ?? ($nameParts[1] ?? ''));

            $shopName = $this->fixMojibake($meta['dokan_store_name'] ?? $seller->display_name);
            $phone = $meta['billing_phone'] ?? '';

            // Dokan payment info (bank details)
            $dokanPayment = isset($meta['dokan_payment'])
                ? @unserialize($meta['dokan_payment'])
                : [];

            $sellerId = DB::table('sellers')->insertGetId([
                'f_name' => $firstName,
                'l_name' => $lastName,
                'phone' => $phone,
                'email' => $emailLower,
                'password' => $seller->user_pass, // WP hashed password (keep as-is)
                'status' => 'approved',
                'sales_commission_percentage' => 0,
                'pos_status' => 'inactive',
                'gst' => null,
                'created_at' => $seller->user_registered,
                'updated_at' => now(),
            ]);

            // Create the shop for this seller
            $shopSlug = Str::slug($shopName) ?: Str::slug($this->fixMojibake($seller->user_login));
            DB::table('shops')->insertGetId([
                'seller_id' => $sellerId,
                'author_type' => 'seller',
                'name' => $shopName,
                'slug' => $shopSlug . '-' . $sellerId,
                'address' => $this->fixMojibake($meta['billing_address_1'] ?? ''),
                'contact' => $phone,
                'temporary_close' => 0,
                'vacation_status' => 'close',
                'created_at' => $seller->user_registered,
                'updated_at' => now(),
            ]);

            $wpToLaravelSellerMap[$seller->ID] = $sellerId;
            $inserted++;
        }

        $bar->finish();
        $this->info('');
        $this->info("   ✅ Sellers: {$inserted} inserted, {$skipped} skipped.");

        cache(['wp_seller_map' => $wpToLaravelSellerMap], now()->addHours(2));
    }

    // =========================================================================
    // STEP 3.5: BRANCHES (CL Branches)
    // =========================================================================

    private function migrateBranches(): void
    {
        $this->info('');
        $this->info('🌿 Step 3.5: Migrating Branches...');

        $wpBranches = DB::connection('mysql_wordpress')
            ->table('wp_cl_branches')
            ->select('*')
            ->get();

        $this->info("   Found {$wpBranches->count()} branches in WordPress.");

        if ($this->dryRun) {
            $this->table(
                ['WP ID', 'Name', 'Vendor WP ID', 'Delegate ID'],
                $wpBranches->map(fn($b) => [
                    $b->id,
                    $b->name,
                    $b->vendor_id,
                    $b->delegate_id
                ])->toArray()
            );
            return;
        }

        $sellerMap = $this->loadSellerMap();
        $defaultSeller = DB::table('sellers')->first();

        $inserted = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($wpBranches->count());
        $bar->start();

        foreach ($wpBranches as $branch) {
            $bar->advance();

            if ($this->skipExisting) {
                $existing = DB::table('branches')->where('name', $branch->name)->value('id');
                if ($existing) {
                    $skipped++;
                    continue;
                }
            }

            $laravelSellerId = $sellerMap[$branch->vendor_id] ?? ($defaultSeller?->id ?? 1);

            $branchId = DB::table('branches')->insertGetId([
                'id' => $branch->id,
                'name' => $branch->name,
                'vendor_id' => $laravelSellerId,
                'delegate_id' => $branch->delegate_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Migrate meta for this branch
            $wpMeta = DB::connection('mysql_wordpress')
                ->table('wp_cl_branchmeta')
                ->where('branch_id', $branch->id)
                ->get();

            foreach ($wpMeta as $m) {
                DB::table('branchmeta')->insert([
                    'meta_id' => $m->meta_id,
                    'branch_id' => $branchId,
                    'meta_key' => $m->meta_key,
                    'meta_value' => $m->meta_value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $inserted++;
        }

        $bar->finish();
        $this->info('');
        $this->info("   ✅ Branches: {$inserted} inserted, {$skipped} skipped.");
    }

    // =========================================================================
    // STEP 4: CUSTOMERS (WordPress regular users)
    // =========================================================================

    private function migrateCustomers(): void
    {
        $this->info('');
        $this->info('👥 Step 4: Migrating Customers...');

        // Get regular customers (not admins/sellers/vendors)
        $wpCustomers = DB::connection('mysql_wordpress')
            ->table('wp_users')
            ->join('wp_usermeta as cap', function ($join) {
                $join->on('wp_users.ID', '=', 'cap.user_id')
                    ->where('cap.meta_key', '=', 'wp_capabilities');
            })
            ->where(function ($q) {
                $q->where('cap.meta_value', 'like', '%customer%')
                    ->orWhere('cap.meta_value', 'like', '%subscriber%')
                    ->orWhere('cap.meta_value', 'like', '%{}%'); // Empty role = subscriber
            })
            ->select('wp_users.*')
            ->when($this->limit > 0, fn($q) => $q->limit($this->limit))
            ->get();

        $this->info("   Found {$wpCustomers->count()} customers in WordPress.");

        if ($this->dryRun) {
            $this->table(
                ['WP ID', 'Login', 'Email', 'Display Name', 'Registered'],
                $wpCustomers->take(10)->map(fn($c) => [
                    $c->ID,
                    $c->user_login,
                    $c->user_email,
                    $c->display_name,
                    $c->user_registered
                ])->toArray()
            );
            $this->warn("   (Showing first 10 of {$wpCustomers->count()})");
            return;
        }

        $wpToLaravelCustomerMap = [];
        $inserted = 0;
        $skipped = 0;
        $processedEmails = [];

        $bar = $this->output->createProgressBar($wpCustomers->count());
        $bar->start();

        // Get all customer meta in bulk for performance
        $allMeta = DB::connection('mysql_wordpress')
            ->table('wp_usermeta')
            ->whereIn('user_id', $wpCustomers->pluck('ID'))
            ->whereIn('meta_key', [
                'first_name',
                'last_name',
                'billing_phone',
                'billing_address_1',
                'billing_city',
                'billing_postcode',
                'billing_country',
            ])
            ->get()
            ->groupBy('user_id');

        foreach ($wpCustomers as $customer) {
            $bar->advance();

            $email = trim($customer->user_email);
            if (empty($email)) {
                $email = $customer->user_login . '@noemail.com';
            }
            $emailLower = strtolower($email);

            if (isset($processedEmails[$emailLower]) || DB::table('users')->where('email', $emailLower)->exists()) {
                $existingId = DB::table('users')->where('email', $emailLower)->value('id');
                if ($existingId) {
                    $wpToLaravelCustomerMap[$customer->ID] = $existingId;
                }
                $skipped++;
                continue;
            }

            $processedEmails[$emailLower] = true;

            $meta = ($allMeta[$customer->ID] ?? collect())->pluck('meta_value', 'meta_key')->toArray();

            $nameParts = explode(' ', $customer->display_name, 2);
            $firstName = $this->fixMojibake($meta['first_name'] ?? $nameParts[0] ?? $seller->user_login ?? $customer->user_login);
            $lastName = $this->fixMojibake($meta['last_name'] ?? ($nameParts[1] ?? ''));

            $userId = DB::table('users')->insertGetId([
                'name' => $this->fixMojibake($customer->display_name ?: $customer->user_login),
                'f_name' => $firstName,
                'l_name' => $lastName,
                'phone' => $meta['billing_phone'] ?? '',
                'email' => $emailLower,
                'password' => $customer->user_pass, // WP hashed password
                'is_active' => 1,
                'is_email_verified' => 1,
                'is_phone_verified' => 0,
                'wallet_balance' => 0,
                'loyalty_point' => 0,
                'street_address' => $this->fixMojibake($meta['billing_address_1'] ?? null),
                'city' => $this->fixMojibake($meta['billing_city'] ?? null),
                'zip' => $meta['billing_postcode'] ?? null,
                'country' => $meta['billing_country'] ?? null,
                'created_at' => $customer->user_registered,
                'updated_at' => now(),
            ]);

            $wpToLaravelCustomerMap[$customer->ID] = $userId;
            $inserted++;
        }

        $bar->finish();
        $this->info('');
        $this->info("   ✅ Customers: {$inserted} inserted, {$skipped} skipped.");

        cache(['wp_customer_map' => $wpToLaravelCustomerMap], now()->addHours(2));
    }

    // =========================================================================
    // STEP 5: PRODUCTS (WooCommerce Products)
    // =========================================================================

    private function migrateProducts(): void
    {
        $this->info('');
        $this->info('📦 Step 5: Migrating Products...');

        // Get all published products from WordPress
        $wpProducts = DB::connection('mysql_wordpress')
            ->table('wp_posts')
            ->where('post_type', 'product')
            ->whereIn('post_status', ['publish', 'draft'])
            ->select([
                'ID',
                'post_author',
                'post_date',
                'post_content',
                'post_title',
                'post_name',
                'post_status',
                'post_parent',
                'post_modified',
            ])
            ->when($this->limit > 0, fn($q) => $q->limit($this->limit))
            ->get();

        $this->info("   Found {$wpProducts->count()} products in WordPress.");

        if ($this->dryRun) {
            $this->table(
                ['WP ID', 'Title', 'Slug', 'Author', 'Status', 'Date'],
                $wpProducts->take(10)->map(fn($p) => [
                    $p->ID,
                    mb_substr($p->post_title, 0, 40),
                    $p->post_name,
                    $p->post_author,
                    $p->post_status,
                    $p->post_date
                ])->toArray()
            );
            $this->warn("   (Showing first 10 of {$wpProducts->count()})");
            return;
        }

        // Load maps (cache → DB fallback)
        $categoryMap = $this->loadCategoryMap();
        $brandMap = $this->loadBrandMap();
        $sellerMap = $this->loadSellerMap();

        // Get default seller (admin seller or first seller)
        $defaultSeller = DB::table('sellers')->first();
        $defaultShop = $defaultSeller ? DB::table('shops')->where('seller_id', $defaultSeller->id)->first() : null;

        $inserted = 0;
        $skipped = 0;
        $wpToLaravelProductMap = [];

        $bar = $this->output->createProgressBar($wpProducts->count());
        $bar->start();

        // Get all meta for products in bulk
        $allPostMeta = DB::connection('mysql_wordpress')
            ->table('wp_postmeta')
            ->whereIn('post_id', $wpProducts->pluck('ID'))
            ->whereIn('meta_key', [
                '_price',
                '_regular_price',
                '_sale_price',
                '_stock',
                '_sku',
                '_thumbnail_id',
                '_product_image_gallery',
                '_product_attributes',
                '_virtual',
                '_downloadable',
                '_weight',
                '_length',
                '_width',
                '_height',
                '_visibility',
                '_featured',
                '_stock_status',
                'total_sales',
                '_wc_review_count',
                '_wc_average_rating',
            ])
            ->get()
            ->groupBy('post_id');

        // Get product categories mapping for all products
        $productCategoryMap = DB::connection('mysql_wordpress')
            ->table('wp_term_relationships')
            ->join('wp_term_taxonomy', 'wp_term_relationships.term_taxonomy_id', '=', 'wp_term_taxonomy.term_taxonomy_id')
            ->whereIn('wp_term_relationships.object_id', $wpProducts->pluck('ID'))
            ->where('wp_term_taxonomy.taxonomy', 'product_cat')
            ->select('wp_term_relationships.object_id', 'wp_term_taxonomy.term_id')
            ->get()
            ->groupBy('object_id');

        // Get product brand mapping for all products
        $productBrandMap = DB::connection('mysql_wordpress')
            ->table('wp_term_relationships')
            ->join('wp_term_taxonomy', 'wp_term_relationships.term_taxonomy_id', '=', 'wp_term_taxonomy.term_taxonomy_id')
            ->whereIn('wp_term_relationships.object_id', $wpProducts->pluck('ID'))
            ->where('wp_term_taxonomy.taxonomy', 'pa_brand')
            ->select('wp_term_relationships.object_id', 'wp_term_taxonomy.term_id')
            ->get()
            ->groupBy('object_id');

        // Get all product attributes terms for all products
        $productAttributesMap = DB::connection('mysql_wordpress')
            ->table('wp_term_relationships')
            ->join('wp_term_taxonomy', 'wp_term_relationships.term_taxonomy_id', '=', 'wp_term_taxonomy.term_taxonomy_id')
            ->join('wp_terms', 'wp_term_taxonomy.term_id', '=', 'wp_terms.term_id')
            ->whereIn('wp_term_relationships.object_id', $wpProducts->pluck('ID'))
            ->where('wp_term_taxonomy.taxonomy', 'like', 'pa_%')
            ->where('wp_term_taxonomy.taxonomy', '!=', 'pa_brand')
            ->select('wp_term_relationships.object_id', 'wp_terms.name', 'wp_term_taxonomy.taxonomy')
            ->get()
            ->groupBy('object_id');

        foreach ($wpProducts as $product) {
            $bar->advance();

            if ($this->skipExisting) {
                $slug = Str::slug($product->post_name ?: $product->post_title);
                $existing = DB::table('products')->where('slug', $slug)->value('id');
                if ($existing) {
                    $wpToLaravelProductMap[$product->ID] = $existing;
                    $skipped++;
                    continue;
                }
            }

            $meta = ($allPostMeta[$product->ID] ?? collect())->pluck('meta_value', 'meta_key')->toArray();

            // Determine seller and shop
            $laravelSellerId = $sellerMap[$product->post_author] ?? ($defaultSeller?->id);
            $laravelShopId = $laravelSellerId
                ? (DB::table('shops')->where('seller_id', $laravelSellerId)->value('id') ?? $defaultShop?->id)
                : $defaultShop?->id;

            $addedBy = $laravelSellerId ? 'seller' : 'admin';

            // Category mapping
            $productCategories = $productCategoryMap[$product->ID] ?? collect();
            $mainCategoryId = null;
            $subCategoryId = null;
            $subSubCategoryId = null;
            $categoryIds = [];

            foreach ($productCategories as $pc) {
                if (isset($categoryMap[$pc->term_id])) {
                    $categoryIds[] = $categoryMap[$pc->term_id];
                }
            }

            if (count($categoryIds) > 0) {
                $mainCategoryId = $categoryIds[0] ?? null;
                $subCategoryId = $categoryIds[1] ?? null;
                $subSubCategoryId = $categoryIds[2] ?? null;
            }

            // Brand mapping
            $productBrands = $productBrandMap[$product->ID] ?? collect();
            $brandId = null;
            foreach ($productBrands as $pb) {
                if (isset($brandMap[$pb->term_id])) {
                    $brandId = $brandMap[$pb->term_id];
                    break;
                }
            }

            // Pricing
            $price = (float) ($meta['_regular_price'] ?? $meta['_price'] ?? 0);
            $salePrice = (float) ($meta['_sale_price'] ?? 0);
            $discount = 0;
            $discountType = 'percent';

            if ($salePrice > 0 && $price > 0) {
                $discount = round((($price - $salePrice) / $price) * 100, 2);
                $discountType = 'percent';
            }

            // Stock
            $stock = (int) ($meta['_stock'] ?? 100);
            if ($stock < 0)
                $stock = 0;

            // Thumbnail
            $thumbnailUrl = null;
            if (isset($meta['_thumbnail_id'])) {
                $attachmentGuid = DB::connection('mysql_wordpress')
                    ->table('wp_posts')
                    ->where('ID', $meta['_thumbnail_id'])
                    ->value('guid');
                if ($attachmentGuid) {
                    $thumbnailUrl = $this->extractWordPressFilename($attachmentGuid);
                }
            }

            // Gallery images
            $galleryImages = [];
            if (isset($meta['_product_image_gallery']) && !empty($meta['_product_image_gallery'])) {
                $galleryIds = explode(',', $meta['_product_image_gallery']);
                foreach ($galleryIds as $galleryId) {
                    $galleryGuid = DB::connection('mysql_wordpress')
                        ->table('wp_posts')
                        ->where('ID', trim($galleryId))
                        ->value('guid');
                    if ($galleryGuid) {
                        $galleryImages[] = $this->extractWordPressFilename($galleryGuid);
                    }
                }
            }

            // Attribute and Specifications mapping
            $productAttrs = $productAttributesMap[$product->ID] ?? collect();
            $attributesTerms = [];
            foreach ($productAttrs as $term) {
                $attrSlug = substr($term->taxonomy, 3); // strip 'pa_'
                $attributesTerms[$attrSlug][] = $this->fixMojibake($term->name);
            }

            $productAttributes = [];
            $choiceOptions = [];

            foreach ($attributesTerms as $attrSlug => $options) {
                $wpAttrLabel = DB::connection('mysql_wordpress')
                    ->table('wp_woocommerce_attribute_taxonomies')
                    ->where('attribute_name', $attrSlug)
                    ->value('attribute_label');
                
                $label = $wpAttrLabel ?: $attrSlug;
                $label = $this->fixMojibake($label);

                $laravelAttrId = $this->getOrCreateAttribute($attrSlug, $label);

                $productAttributes[] = (string)$laravelAttrId;
                $choiceOptions[] = [
                    'name' => 'choice_' . $laravelAttrId,
                    'title' => $label,
                    'options' => $options
                ];
            }

            // Slug
            $slug = Str::slug($this->fixMojibake($product->post_name ?: $product->post_title));
            if (empty($slug)) {
                $slug = 'product-' . $product->ID;
            }

            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('products')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $details = $product->post_content ?? '';
            $fixedName = $this->fixMojibake($product->post_title);
            $fixedDetails = $this->fixMojibake($details);

            $laravelProductId = DB::table('products')->insertGetId([
                'user_id' => $laravelSellerId,
                'shop_id' => $laravelShopId,
                'added_by' => $addedBy,
                'name' => $fixedName,
                'slug' => $slug,
                'code' => $meta['_sku'] ?? 'WP-' . $product->ID,
                'category_ids' => json_encode(array_values(array_filter($categoryIds))),
                'category_id' => $mainCategoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => $subSubCategoryId,
                'brand_id' => $brandId,
                'unit' => 'حبة',
                'product_type' => 'physical',
                'digital_product_type' => 'ready_product',
                'details' => $fixedDetails,
                'images' => json_encode($galleryImages),
                'color_image' => json_encode([]),
                'thumbnail' => $thumbnailUrl ?? '',
                'thumbnail_storage_type' => 'external_storage',
                'unit_price' => $price ?: 1,
                'purchase_price' => 0,
                'tax' => 15,
                'tax_type' => 'percent',
                'tax_model' => 'exclude',
                'discount' => $discount,
                'discount_type' => $discountType,
                'current_stock' => $stock,
                'minimum_order_qty' => 1,
                'min_qty' => 1,
                'status' => $product->post_status === 'publish' ? 1 : 0,
                'featured_status' => isset($meta['_featured']) && $meta['_featured'] === 'yes' ? 1 : 0,
                'featured' => 0,
                'flash_deal' => 0,
                'request_status' => 1,
                'free_shipping' => 0,
                'shipping_cost' => 0,
                'refundable' => 1,
                'published' => $product->post_status === 'publish' ? 1 : 0,
                'colors' => json_encode([]),
                'choice_options' => json_encode($choiceOptions),
                'variation' => json_encode([]),
                'attributes' => json_encode($productAttributes),
                'multiply_qty' => 0,
                'temp_shipping_cost' => 0,
                'is_shipping_cost_updated' => 0,
                'variant_product' => 0,
                'video_provider' => 'youtube',
                'video_url' => null,
                'meta_title' => $fixedName,
                'meta_description' => null,
                'meta_image' => null,
                'denied_note' => null,
                'created_at' => $product->post_date,
                'updated_at' => $product->post_modified,
            ]);

            // Add translations for products name and details
            $this->updateOrCreateTranslation('App\Models\Product', $laravelProductId, 'ar', 'name', $fixedName);
            $this->updateOrCreateTranslation('App\Models\Product', $laravelProductId, 'ar', 'details', $fixedDetails);
            $this->updateOrCreateTranslation('App\Models\Product', $laravelProductId, 'en', 'name', $fixedName);
            $this->updateOrCreateTranslation('App\Models\Product', $laravelProductId, 'en', 'details', $fixedDetails);

            // Parse branch IDs from cl_branches metadata
            if (isset($meta['cl_branches'])) {
                $branchIds = @json_decode($meta['cl_branches'], true);
                if (is_array($branchIds)) {
                    foreach ($branchIds as $bId) {
                        if (DB::table('branches')->where('id', $bId)->exists()) {
                            DB::table('branch_product')->insertOrIgnore([
                                'branch_id' => $bId,
                                'product_id' => $laravelProductId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            $wpToLaravelProductMap[$product->ID] = $laravelProductId;
            $inserted++;
        }

        $bar->finish();
        $this->info('');
        $this->info("   ✅ Products: {$inserted} inserted, {$skipped} skipped.");

        cache(['wp_product_map' => $wpToLaravelProductMap], now()->addHours(2));
    }

    // =========================================================================
    // STEP 6: ORDERS (Dokan/WooCommerce Orders)
    // =========================================================================

    private function migrateOrders(): void
    {
        $this->info('');
        $this->info('🛒 Step 6: Migrating Orders...');

        // WooCommerce orders are stored as wp_posts with post_type = 'shop_order'
        $wpOrders = DB::connection('mysql_wordpress')
            ->table('wp_posts')
            ->where('post_type', 'shop_order')
            ->whereIn('post_status', [
                'wc-completed',
                'wc-processing',
                'wc-pending',
                'wc-on-hold',
                'wc-cancelled',
                'wc-refunded',
            ])
            ->select(['ID', 'post_author', 'post_date', 'post_status', 'post_modified'])
            ->when($this->limit > 0, fn($q) => $q->limit($this->limit))
            ->get();

        $this->info("   Found {$wpOrders->count()} orders in WordPress.");

        if ($this->dryRun) {
            $this->table(
                ['WP ID', 'Status', 'Date'],
                $wpOrders->take(10)->map(fn($o) => [
                    $o->ID,
                    $o->post_status,
                    $o->post_date
                ])->toArray()
            );
            $this->warn("   (Showing first 10 of {$wpOrders->count()})");
            return;
        }

        // Load maps (cache → DB fallback)
        $customerMap = $this->loadCustomerMap();
        $sellerMap = $this->loadSellerMap();
        $productMap = $this->loadProductMap();
        $defaultShippingMethodId = DB::table('shipping_methods')->value('id') ?? 1;

        $inserted = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($wpOrders->count());
        $bar->start();

        // Get all order meta in bulk
        $allOrderMeta = DB::connection('mysql_wordpress')
            ->table('wp_postmeta')
            ->whereIn('post_id', $wpOrders->pluck('ID'))
            ->whereIn('meta_key', [
                '_order_total',
                '_order_tax',
                '_order_shipping',
                '_billing_email',
                '_billing_first_name',
                '_billing_last_name',
                '_billing_address_1',
                '_billing_city',
                '_billing_phone',
                '_billing_country',
                '_shipping_address_1',
                '_shipping_city',
                '_shipping_country',
                '_payment_method',
                '_payment_method_title',
                '_customer_user',
                '_order_currency',
                '_order_discount',
                '_cart_discount',
                '_transaction_id',
                '_order_key',
            ])
            ->get()
            ->groupBy('post_id');

        // Get Dokan order info (vendor per order)
        $dokanOrders = DB::connection('mysql_wordpress')
            ->table('wp_dokan_orders')
            ->whereIn('order_id', $wpOrders->pluck('ID'))
            ->get()
            ->keyBy('order_id');

        foreach ($wpOrders as $order) {
            $bar->advance();

            $meta = ($allOrderMeta[$order->ID] ?? collect())->pluck('meta_value', 'meta_key')->toArray();
            $dokanOrder = $dokanOrders[$order->ID] ?? null;

            // Map customer
            $wpCustomerId = (int) ($meta['_customer_user'] ?? 0);
            $laravelCustomerId = $customerMap[$wpCustomerId] ?? null;

            // Map seller
            $wpSellerId = $dokanOrder ? $dokanOrder->seller_id : 0;
            $laravelSellerId = $sellerMap[$wpSellerId] ?? null;

            $laravelShopId = $laravelSellerId
                ? DB::table('shops')->where('seller_id', $laravelSellerId)->value('id')
                : null;

            // Order status mapping
            $orderStatus = $this->mapOrderStatus($order->post_status);
            $paymentStatus = in_array($order->post_status, ['wc-completed', 'wc-processing']) ? 'paid' : 'unpaid';

            // Order amounts
            $orderAmount = (float) ($meta['_order_total'] ?? 0);
            $shippingCost = (float) ($meta['_order_shipping'] ?? 0);
            $discount = (float) ($meta['_cart_discount'] ?? $meta['_order_discount'] ?? 0);

            // Build shipping address JSON
            $shippingAddressData = json_encode([
                'contact_person_name' => trim($this->fixMojibake(($meta['_billing_first_name'] ?? '') . ' ' . ($meta['_billing_last_name'] ?? ''))),
                'address_type' => 'home',
                'address' => $this->fixMojibake($meta['_shipping_address_1'] ?? $meta['_billing_address_1'] ?? ''),
                'city' => $this->fixMojibake($meta['_shipping_city'] ?? $meta['_billing_city'] ?? ''),
                'country' => $meta['_shipping_country'] ?? $meta['_billing_country'] ?? '',
                'phone' => $meta['_billing_phone'] ?? '',
                'email' => $meta['_billing_email'] ?? '',
            ]);

            $orderGroupId = Str::uuid()->toString();

            $laravelOrderId = DB::table('orders')->insertGetId([
                'customer_id' => $laravelCustomerId,
                'customer_type' => 'customer',
                'seller_id' => $laravelSellerId,
                'seller_is' => $laravelSellerId ? 'seller' : 'admin',
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'payment_method' => $meta['_payment_method'] ?? 'cash_on_delivery',
                'transaction_ref' => $meta['_transaction_id'] ?? null,
                'order_amount' => $orderAmount,
                'admin_commission' => 0,
                'shipping_address' => null,
                'shipping_address_data' => $shippingAddressData,
                'shipping_cost' => $shippingCost,
                'shipping_method_id' => $defaultShippingMethodId,
                'discount_amount' => $discount,
                'discount_type' => 'amount',
                'is_guest' => $laravelCustomerId ? 0 : 1,
                'order_group_id' => $orderGroupId,
                'verification_code' => rand(1000, 9999),
                'verification_status' => 0,
                'checked' => 0,
                'created_at' => $order->post_date,
                'updated_at' => $order->post_modified,
            ]);

            // Get order items and insert order details
            $this->migrateOrderItems($order->ID, $laravelOrderId, $productMap);

            $inserted++;
        }

        $bar->finish();
        $this->info('');
        $this->info("   ✅ Orders: {$inserted} inserted, {$skipped} skipped.");
    }

    // =========================================================================
    // ORDER ITEMS
    // =========================================================================

    private function migrateOrderItems(int $wpOrderId, int $laravelOrderId, array $productMap): void
    {
        // Get WooCommerce order items
        $wpItems = DB::connection('mysql_wordpress')
            ->table('wp_woocommerce_order_items')
            ->where('order_id', $wpOrderId)
            ->where('order_item_type', 'line_item')
            ->get();

        if ($wpItems->isEmpty()) {
            return;
        }

        // Get item meta
        $allItemMeta = DB::connection('mysql_wordpress')
            ->table('wp_woocommerce_order_itemmeta')
            ->whereIn('order_item_id', $wpItems->pluck('order_item_id'))
            ->whereIn('meta_key', [
                '_product_id',
                '_variation_id',
                '_qty',
                '_line_total',
                '_line_tax',
                '_line_subtotal',
                '_line_subtotal_tax',
            ])
            ->get()
            ->groupBy('order_item_id');

        foreach ($wpItems as $item) {
            $meta = ($allItemMeta[$item->order_item_id] ?? collect())->pluck('meta_value', 'meta_key')->toArray();

            $wpProductId = (int) ($meta['_product_id'] ?? 0);
            $laravelProductId = $productMap[$wpProductId] ?? null;

            $qty = (int) ($meta['_qty'] ?? 1);
            $unitPrice = (float) ($meta['_line_total'] ?? 0) / max($qty, 1);
            $tax = (float) ($meta['_line_tax'] ?? 0);

            // Get product info for snapshot
            $product = $laravelProductId
                ? DB::table('products')->where('id', $laravelProductId)->first()
                : null;

            $defaultShippingMethodId = DB::table('shipping_methods')->value('id') ?? 1;

            DB::table('order_details')->insert([
                'order_id' => $laravelOrderId,
                'product_id' => $laravelProductId,
                'seller_id' => $product->user_id ?? null,
                'product_details' => json_encode([
                    'id' => $laravelProductId,
                    'name' => $this->fixMojibake($product->name ?? $item->order_item_name),
                    'thumbnail' => $product->thumbnail ?? null,
                    'unit_price' => $unitPrice,
                ]),
                'qty' => $qty,
                'price' => $unitPrice,
                'tax' => $tax / max($qty, 1),
                'tax_model' => 'exclude',
                'discount' => 0,
                'discount_type' => 'flat',
                'variant' => null,
                'variation' => json_encode([]),
                'delivery_status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_method_id' => $defaultShippingMethodId,
                'is_stock_decreased' => 1,
                'refund_request' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // =========================================================================
    // MAP LOADERS (Cache → DB fallback)
    // =========================================================================

    /**
     * Load WP term_id → Laravel category id map.
     * Uses cache if available, otherwise rebuilds from DB via slug matching.
     */
    private function loadCategoryMap(): array
    {
        $cached = cache('wp_category_map', []);
        if (!empty($cached)) {
            return $cached;
        }

        $this->warn('   ⚡ Cache miss: rebuilding category map from DB...');

        $wpCats = DB::connection('mysql_wordpress')
            ->table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->where('wp_term_taxonomy.taxonomy', 'product_cat')
            ->select('wp_terms.term_id', 'wp_terms.slug')
            ->get();

        $laravelCats = DB::table('categories')
            ->whereNotNull('slug')
            ->pluck('id', 'slug')
            ->toArray();

        $map = [];
        foreach ($wpCats as $cat) {
            $slug = Str::slug(urldecode($cat->slug));
            if (isset($laravelCats[$slug])) {
                $map[$cat->term_id] = $laravelCats[$slug];
            }
        }

        cache(['wp_category_map' => $map], now()->addHours(2));
        return $map;
    }

    /**
     * Load WP term_id → Laravel brand id map.
     */
    private function loadBrandMap(): array
    {
        $cached = cache('wp_brand_map', []);
        if (!empty($cached)) {
            return $cached;
        }

        $this->warn('   ⚡ Cache miss: rebuilding brand map from DB...');

        $wpBrands = DB::connection('mysql_wordpress')
            ->table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->where('wp_term_taxonomy.taxonomy', 'pa_brand')
            ->select('wp_terms.term_id', 'wp_terms.slug')
            ->get();

        $laravelBrands = DB::table('brands')
            ->whereNotNull('slug')
            ->pluck('id', 'slug')
            ->toArray();

        $map = [];
        foreach ($wpBrands as $brand) {
            $slug = Str::slug(urldecode($brand->slug));
            if (isset($laravelBrands[$slug])) {
                $map[$brand->term_id] = $laravelBrands[$slug];
            }
        }

        cache(['wp_brand_map' => $map], now()->addHours(2));
        return $map;
    }

    /**
     * Load WP user_id → Laravel seller id map.
     */
    private function loadSellerMap(): array
    {
        $cached = cache('wp_seller_map', []);
        if (!empty($cached)) {
            return $cached;
        }

        $this->warn('   ⚡ Cache miss: rebuilding seller map from DB...');

        $wpSellers = DB::connection('mysql_wordpress')
            ->table('wp_users')
            ->join('wp_usermeta as cap', function ($join) {
                $join->on('wp_users.ID', '=', 'cap.user_id')
                    ->where('cap.meta_key', '=', 'wp_capabilities');
            })
            ->where(function ($q) {
                $q->where('cap.meta_value', 'like', '%seller%')
                    ->orWhere('cap.meta_value', 'like', '%vendor%');
            })
            ->select('wp_users.ID', 'wp_users.user_email')
            ->get();

        $laravelSellers = DB::table('sellers')
            ->pluck('id', 'email')
            ->toArray();

        $map = [];
        foreach ($wpSellers as $s) {
            if (isset($laravelSellers[$s->user_email])) {
                $map[$s->ID] = $laravelSellers[$s->user_email];
            }
        }

        cache(['wp_seller_map' => $map], now()->addHours(2));
        return $map;
    }

    /**
     * Load WP user_id → Laravel user id map for customers.
     */
    private function loadCustomerMap(): array
    {
        $cached = cache('wp_customer_map', []);
        if (!empty($cached)) {
            return $cached;
        }

        $this->warn('   ⚡ Cache miss: rebuilding customer map from DB...');

        $wpCustomers = DB::connection('mysql_wordpress')
            ->table('wp_users')
            ->select('ID', 'user_email')
            ->get();

        $laravelUsers = DB::table('users')
            ->whereNotNull('email')
            ->pluck('id', 'email')
            ->toArray();

        $map = [];
        foreach ($wpCustomers as $c) {
            if (isset($laravelUsers[$c->user_email])) {
                $map[$c->ID] = $laravelUsers[$c->user_email];
            }
        }

        cache(['wp_customer_map' => $map], now()->addHours(2));
        return $map;
    }

    /**
     * Load WP post_id → Laravel product id map.
     */
    private function loadProductMap(): array
    {
        $cached = cache('wp_product_map', []);
        if (!empty($cached)) {
            return $cached;
        }

        $this->warn('   ⚡ Cache miss: rebuilding product map from DB...');

        $wpProducts = DB::connection('mysql_wordpress')
            ->table('wp_posts')
            ->where('post_type', 'product')
            ->select('ID', 'post_name', 'post_title')
            ->get();

        $laravelProducts = DB::table('products')
            ->whereNotNull('slug')
            ->pluck('id', 'slug')
            ->toArray();

        $map = [];
        foreach ($wpProducts as $p) {
            $slug = Str::slug(urldecode($p->post_name ?: $p->post_title));
            if (isset($laravelProducts[$slug])) {
                $map[$p->ID] = $laravelProducts[$slug];
            }
        }

        cache(['wp_product_map' => $map], now()->addHours(2));
        return $map;
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Extract filename from WordPress attachment URL for external storage reference.
     * Uses the full URL so products can still show images from the original domain.
     */
    private function extractWordPressFilename(string $url): string
    {
        // Return the full WordPress URL - the product uses external_storage_type
        // so it will be treated as an absolute URL
        return $url;
    }

    /**
     * Clean WordPress content by removing shortcodes and fixing HTML.
     */
    private function cleanWordPressContent(string $content): string
    {
        // Remove WordPress shortcodes
        $content = preg_replace('/\[[\w\s\/="\']+\]/', '', $content);

        // Remove <!-- --> comments
        $content = preg_replace('/<!--(.|\n)*?-->/', '', $content);

        // Clean up whitespace
        $content = trim($content);

        return $content ?: '';
    }

    /**
     * Map WooCommerce order status to 6Valley order status.
     */
    private function mapOrderStatus(string $wcStatus): string
    {
        return match ($wcStatus) {
            'wc-completed' => 'delivered',
            'wc-processing' => 'confirmed',
            'wc-pending' => 'pending',
            'wc-on-hold' => 'confirmed',
            'wc-cancelled' => 'canceled',
            'wc-refunded' => 'returned',
            'wc-failed' => 'failed',
        };
    }

    /**
     * Fix Arabic encoding Mojibake caused by CP850 to UTF-8 double encoding.
     */
    private function fixMojibake(?string $string): ?string
    {
        if (empty($string)) {
            return $string;
        }

        // Convert from UTF-8 to CP850 bytes, which are the actual UTF-8 bytes of the Arabic string
        $fixed = @iconv('UTF-8', 'CP850//IGNORE', $string);
        
        return $fixed ?: $string;
    }

    /**
     * Get or create attribute helper.
     */
    private function getOrCreateAttribute(string $name, string $label): int
    {
        $existingId = DB::table('attributes')->where('name', $name)->value('id');
        if ($existingId) {
            return $existingId;
        }

        $attrId = DB::table('attributes')->insertGetId([
            'name'       => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add translations for the label
        $this->updateOrCreateTranslation('App\Models\Attribute', $attrId, 'ar', 'name', $label);
        $this->updateOrCreateTranslation('App\Models\Attribute', $attrId, 'en', 'name', $label);

        return $attrId;
    }

    /**
     * Update or Create Translation helper.
     */
    private function updateOrCreateTranslation(string $model, int $id, string $locale, string $key, ?string $value): void
    {
        if ($value === null) {
            return;
        }

        DB::table('translations')->updateOrInsert(
            [
                'translationable_type' => $model,
                'translationable_id'   => $id,
                'locale'               => $locale,
                'key'                  => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
