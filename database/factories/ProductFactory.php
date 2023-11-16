<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 10, 2000),
            'brand' => $this->faker->word,
            'processor' => $this->faker->word,
            'ram' => $this->faker->word,
            'storage' => $this->faker->word,
        ];
    }
}
