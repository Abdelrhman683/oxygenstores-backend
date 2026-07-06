<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Shop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-product-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean absolute WordPress image URLs in products and other tables to relative paths';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting database image URLs cleanup...');

        // 1. Clean Products
        $this->cleanProducts();

        // 2. Clean Categories
        $this->cleanGenericTable(Category::class, 'icon');

        // 3. Clean Brands
        $this->cleanGenericTable(Brand::class, 'image');

        // 4. Clean Banners
        $this->cleanGenericTable(Banner::class, 'photo');

        // 5. Clean Shops
        $this->cleanGenericTable(Shop::class, 'image');
        $this->cleanGenericTable(Shop::class, 'banner');
        $this->cleanGenericTable(Shop::class, 'bottom_banner');
        $this->cleanGenericTable(Shop::class, 'offer_banner');

        $this->info('All cleanups completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Clean product thumbnails and gallery images.
     */
    private function cleanProducts()
    {
        $products = Product::all();
        $updatedCount = 0;

        DB::transaction(function () use ($products, &$updatedCount) {
            foreach ($products as $product) {
                $isUpdated = false;

                // Clean thumbnail
                if (!empty($product->thumbnail)) {
                    $cleanedThumbnail = $this->cleanUrl($product->thumbnail);
                    if ($cleanedThumbnail !== $product->thumbnail) {
                        $product->thumbnail = $cleanedThumbnail;
                        $isUpdated = true;
                    }
                }

                // Clean gallery images
                if (!empty($product->images)) {
                    $imagesDecoded = is_array($product->images) ? $product->images : json_decode($product->images, true);
                    if (is_array($imagesDecoded)) {
                        $cleanedImages = [];
                        $imagesChanged = false;
                        foreach ($imagesDecoded as $image) {
                            if (is_string($image)) {
                                $cleaned = $this->cleanUrl($image);
                                if ($cleaned !== $image) {
                                    $imagesChanged = true;
                                }
                                $cleanedImages[] = $cleaned;
                            } elseif (is_array($image) && isset($image['image_name'])) {
                                $cleaned = $this->cleanUrl($image['image_name']);
                                if ($cleaned !== $image['image_name']) {
                                    $imagesChanged = true;
                                    $image['image_name'] = $cleaned;
                                }
                                $cleanedImages[] = $image;
                            } else {
                                $cleanedImages[] = $image;
                            }
                        }

                        if ($imagesChanged) {
                            $product->images = json_encode($cleanedImages);
                            $isUpdated = true;
                        }
                    }
                }

                if ($isUpdated) {
                    $product->save();
                    $updatedCount++;
                }
            }
        });

        $this->info("Updated {$updatedCount} products.");
    }

    /**
     * Clean URLs in a generic table column.
     *
     * @param string $modelClass
     * @param string $column
     */
    private function cleanGenericTable(string $modelClass, string $column)
    {
        $tableName = (new $modelClass)->getTable();
        $records = $modelClass::all();
        $updatedCount = 0;

        DB::transaction(function () use ($records, $column, &$updatedCount) {
            foreach ($records as $record) {
                if (!empty($record->$column)) {
                    $original = $record->$column;
                    $cleaned = $this->cleanUrl($original);
                    if ($cleaned !== $original) {
                        $record->$column = $cleaned;
                        $record->save();
                        $updatedCount++;
                    }
                }
            }
        });

        if ($updatedCount > 0) {
            $this->info("Updated {$updatedCount} rows in table '{$tableName}' (column: '{$column}').");
        }
    }

    /**
     * Clean absolute URL to relative path.
     *
     * @param string $url
     * @return string
     */
    private function cleanUrl(string $url): string
    {
        // If it contains wp-content/uploads/
        if (strpos($url, 'wp-content/uploads/') !== false) {
            $parts = explode('wp-content/uploads/', $url);
            return end($parts);
        }

        // If it starts with http or has a protocol (fallback logic)
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            $parsedUrl = parse_url($url);
            if (isset($parsedUrl['path'])) {
                // Remove leading slash if any
                return ltrim($parsedUrl['path'], '/');
            }
        }

        return $url;
    }
}

