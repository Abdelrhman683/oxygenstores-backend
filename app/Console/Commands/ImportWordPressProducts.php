<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportWordPressProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wp-products {--truncate : Truncate the products and attributes tables first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products, attributes, categories, and brands from the old WordPress WooCommerce database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting WooCommerce migration...');

        // 1. Verify database connection
        try {
            DB::connection('mysql_wordpress')->getPdo();
            $this->info('Connected to WordPress database successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to connect to the WordPress database (mysql_wordpress). ' . $e->getMessage());
            $this->info('Please check config/database.php and make sure DB_WP_DATABASE is set in .env.');
            return 1;
        }

        // 2. Truncate tables if requested
        if ($this->option('truncate')) {
            $this->warn('Truncating local products, attributes, translations, categories, and brands tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Product::truncate();
            Attribute::truncate();
            Category::truncate();
            Brand::truncate();
            Translation::whereIn('translationable_type', [
                Product::class,
                Attribute::class,
                Category::class,
                Brand::class
            ])->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Tables truncated.');
        }

        // 3. Migrate WooCommerce attributes to local attributes table
        $this->info('Migrating attributes...');
        $wpAttributes = DB::connection('mysql_wordpress')
            ->table('wp_woocommerce_attribute_taxonomies')
            ->get();

        foreach ($wpAttributes as $wpAttr) {
            $attrName = $wpAttr->attribute_name; // e.g. 'color'
            $attrLabel = $wpAttr->attribute_label ?: $wpAttr->attribute_name; // e.g. 'اللون'

            $this->getOrCreateAttribute($attrName, $attrLabel);
        }
        $this->info('Attributes migrated successfully.');

        // 4. Retrieve WordPress products
        $this->info('Fetching products from WordPress...');
        $wpProducts = DB::connection('mysql_wordpress')
            ->table('wp_posts')
            ->where('post_type', 'product')
            ->whereIn('post_status', ['publish', 'draft'])
            ->get();

        $total = $wpProducts->count();
        $this->info("Found {$total} products to import.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($wpProducts as $wpProd) {
            $wpId = $wpProd->ID;
            $title = $wpProd->post_title;
            $slug = $wpProd->post_name ?: Str::slug($title);
            $content = $wpProd->post_content;
            $status = $wpProd->post_status === 'publish' ? 1 : 0;

            // Fetch metadata
            $meta = DB::connection('mysql_wordpress')
                ->table('wp_postmeta')
                ->where('post_id', $wpId)
                ->pluck('meta_value', 'meta_key')
                ->toArray();

            $sku = $meta['_sku'] ?? ('WP-PROD-' . $wpId);
            $price = floatval($meta['_price'] ?? 0);
            $regularPrice = floatval($meta['_regular_price'] ?? $price);

            // Calculate discount
            $discount = 0;
            $discountType = 'flat';
            if ($regularPrice > $price && $price > 0) {
                $discount = $regularPrice - $price;
            }

            // Determine stock
            $manageStock = $meta['_manage_stock'] ?? 'no';
            $stockStatus = $meta['_stock_status'] ?? 'instock';
            $stockQty = 0;
            if ($manageStock === 'yes') {
                $stockQty = intval($meta['_stock'] ?? 0);
            } else {
                $stockQty = $stockStatus === 'instock' ? 100 : 0;
            }

            // Images
            $thumbnail = $this->getProductThumbnail($wpId);
            $galleryImages = $this->getProductGallery($wpId);

            // Fetch linked terms (Categories, Brands, Attributes)
            $terms = DB::connection('mysql_wordpress')
                ->table('wp_terms as t')
                ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->join('wp_term_relationships as tr', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
                ->where('tr.object_id', $wpId)
                ->select('t.name', 't.slug', 'tt.taxonomy')
                ->get();

            $categoryIdsArr = [];
            $categoryId = null;
            $brandId = null;
            $productAttributes = [];
            $choiceOptions = [];

            // Group terms by taxonomy for attributes mapping
            $attributesTerms = [];

            foreach ($terms as $term) {
                if ($term->taxonomy === 'product_cat') {
                    // Map or create category
                    $catId = $this->getOrCreateCategory($term->name, $term->slug);
                    $categoryId = $catId;
                    $categoryIdsArr[] = ['id' => (string)$catId, 'position' => 1];
                } elseif ($term->taxonomy === 'pa_brand') {
                    // Map or create brand
                    $brandId = $this->getOrCreateBrand($term->name, $term->slug);
                } elseif (str_starts_with($term->taxonomy, 'pa_')) {
                    $attrSlug = substr($term->taxonomy, 3); // strip 'pa_'
                    $attributesTerms[$attrSlug][] = $term->name;
                }
            }

            // Process product attributes
            foreach ($attributesTerms as $attrSlug => $options) {
                // Find or create attribute general definition
                $wpAttrLabel = DB::connection('mysql_wordpress')
                    ->table('wp_woocommerce_attribute_taxonomies')
                    ->where('attribute_name', $attrSlug)
                    ->value('attribute_label');
                
                $label = $wpAttrLabel ?: $attrSlug;
                $laravelAttr = $this->getOrCreateAttribute($attrSlug, $label);

                $productAttributes[] = (string)$laravelAttr->id;
                $choiceOptions[] = [
                    'name' => 'choice_' . $laravelAttr->id,
                    'title' => $label,
                    'options' => $options
                ];
            }

            // Fallback Brand/Category if not defined
            if (empty($brandId)) {
                $brandId = $this->getOrCreateBrand('غير محدد', 'unbranded');
            }
            if (empty($categoryId)) {
                $categoryId = $this->getOrCreateCategory('عام', 'general');
                $categoryIdsArr[] = ['id' => (string)$categoryId, 'position' => 1];
            }

            // Update or Create Product
            $product = Product::updateOrCreate(
                ['code' => $sku],
                [
                    'added_by' => 'admin',
                    'user_id' => 1,
                    'shop_id' => null,
                    'name' => $title,
                    'slug' => $slug,
                    'product_type' => 'physical',
                    'category_ids' => json_encode($categoryIdsArr),
                    'category_id' => $categoryId,
                    'sub_category_id' => null,
                    'sub_sub_category_id' => null,
                    'brand_id' => $brandId,
                    'unit' => 'pc',
                    'min_qty' => 1,
                    'refundable' => 1,
                    'images' => json_encode($galleryImages),
                    'color_image' => '',
                    'thumbnail' => $thumbnail,
                    'variant_product' => 0,
                    'attributes' => json_encode($productAttributes),
                    'choice_options' => json_encode($choiceOptions),
                    'variation' => json_encode([]),
                    'published' => $status,
                    'unit_price' => $regularPrice ?: $price,
                    'purchase_price' => $regularPrice ?: $price,
                    'tax' => '0.00',
                    'tax_type' => 'percent',
                    'tax_model' => 'exclude',
                    'discount' => $discount,
                    'discount_type' => $discountType,
                    'current_stock' => $stockQty,
                    'minimum_order_qty' => 1,
                    'details' => $content ?: $title,
                    'free_shipping' => 0,
                    'status' => $status,
                    'featured_status' => 0,
                    'meta_title' => $title,
                    'meta_description' => Str::limit(strip_tags($content ?: $title), 150),
                ]
            );

            // Add translations
            $this->updateOrCreateTranslation(Product::class, $product->id, 'ar', 'name', $title);
            $this->updateOrCreateTranslation(Product::class, $product->id, 'ar', 'details', $content ?: $title);
            $this->updateOrCreateTranslation(Product::class, $product->id, 'en', 'name', $title);
            $this->updateOrCreateTranslation(Product::class, $product->id, 'en', 'details', $content ?: $title);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Migration completed successfully!');
        return 0;
    }

    /**
     * Get or create attribute.
     */
    protected function getOrCreateAttribute(string $name, string $label)
    {
        $attribute = Attribute::where('name', $name)->first();

        if (!$attribute) {
            $attribute = Attribute::create([
                'name' => $name,
            ]);
        }

        // Add Arabic/English translations for label
        $this->updateOrCreateTranslation(Attribute::class, $attribute->id, 'ar', 'name', $label);
        $this->updateOrCreateTranslation(Attribute::class, $attribute->id, 'en', 'name', $label);

        return $attribute;
    }

    /**
     * Get or create Category.
     */
    protected function getOrCreateCategory(string $wpName, string $wpSlug)
    {
        $category = Category::where('name', $wpName)->first();
        if ($category) {
            return $category->id;
        }

        // Search translation
        $translation = Translation::where('translationable_type', Category::class)
            ->where('value', $wpName)
            ->first();
        if ($translation) {
            return $translation->translationable_id;
        }

        // Create Category
        $category = Category::create([
            'name' => $wpName,
            'slug' => $wpSlug ?: Str::slug($wpName),
            'parent_id' => 0,
            'position' => 1,
            'home_status' => 1,
            'priority' => 1,
        ]);

        $this->updateOrCreateTranslation(Category::class, $category->id, 'ar', 'name', $wpName);
        $this->updateOrCreateTranslation(Category::class, $category->id, 'en', 'name', $wpName);

        return $category->id;
    }

    /**
     * Get or create Brand.
     */
    protected function getOrCreateBrand(string $wpName, string $wpSlug)
    {
        $brand = Brand::where('name', $wpName)->first();
        if ($brand) {
            return $brand->id;
        }

        $translation = Translation::where('translationable_type', Brand::class)
            ->where('value', $wpName)
            ->first();
        if ($translation) {
            return $translation->translationable_id;
        }

        $brand = Brand::create([
            'name' => $wpName,
            'slug' => $wpSlug ?: Str::slug($wpName),
            'image' => 'def.png',
            'status' => 1,
        ]);

        $this->updateOrCreateTranslation(Brand::class, $brand->id, 'ar', 'name', $wpName);
        $this->updateOrCreateTranslation(Brand::class, $brand->id, 'en', 'name', $wpName);

        return $brand->id;
    }

    /**
     * Helper to get product thumbnail.
     */
    protected function getProductThumbnail($wpProductId)
    {
        $thumbnailId = DB::connection('mysql_wordpress')
            ->table('wp_postmeta')
            ->where('post_id', $wpProductId)
            ->where('meta_key', '_thumbnail_id')
            ->value('meta_value');

        if ($thumbnailId) {
            $file = DB::connection('mysql_wordpress')
                ->table('wp_postmeta')
                ->where('post_id', $thumbnailId)
                ->where('meta_key', '_wp_attached_file')
                ->value('meta_value');

            if ($file) {
                return basename($file);
            }
        }
        return 'def.png';
    }

    /**
     * Helper to get product gallery images.
     */
    protected function getProductGallery($wpProductId)
    {
        $galleryIdsStr = DB::connection('mysql_wordpress')
            ->table('wp_postmeta')
            ->where('post_id', $wpProductId)
            ->where('meta_key', '_product_image_gallery')
            ->value('meta_value');

        $images = [];
        if ($galleryIdsStr) {
            $galleryIds = explode(',', $galleryIdsStr);
            foreach ($galleryIds as $id) {
                if (empty(trim($id))) continue;
                $file = DB::connection('mysql_wordpress')
                    ->table('wp_postmeta')
                    ->where('post_id', trim($id))
                    ->where('meta_key', '_wp_attached_file')
                    ->value('meta_value');

                if ($file) {
                    $images[] = basename($file);
                }
            }
        }
        return $images;
    }

    /**
     * Update or Create Translation helper.
     */
    protected function updateOrCreateTranslation(string $model, int $id, string $locale, string $key, ?string $value)
    {
        if ($value === null) {
            return;
        }

        Translation::updateOrCreate(
            [
                'translationable_type' => $model,
                'translationable_id' => $id,
                'locale' => $locale,
                'key' => $key,
            ],
            [
                'value' => $value
            ]
        );
    }
}
