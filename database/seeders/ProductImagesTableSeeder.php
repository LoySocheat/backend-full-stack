<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;
use App\Models\Product;

class ProductImagesTableSeeder extends Seeder
{
    public function run()
    {
        // Assuming you have products in the database, associate images with each product
        $products = Product::all();

        foreach ($products as $product) {
            // Create two images for each product
            ProductImage::factory()->count(2)->create([
                'product_id' => $product->id,
            ]);
        }
    }
}
