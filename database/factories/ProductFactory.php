<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchasePrice = $this->faker->randomFloat(2, 5, 500);
        $profitMargin = $this->faker->randomFloat(2, 0.2, 0.8); // 20% to 80% profit margin
        $sellingPrice = $purchasePrice * (1 + $profitMargin);
        
        return [
            'name' => $this->faker->words(3, true),
            'code' => strtoupper($this->faker->unique()->bothify('PRD###??')),
            'barcode' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->sentence(15),
            'category_id' => Category::factory(),
            'purchase_price' => $purchasePrice,
            'selling_price' => $sellingPrice,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'min_stock_level' => $this->faker->numberBetween(5, 15),
            'max_stock_level' => $this->faker->numberBetween(100, 1000),
            'unit' => $this->faker->randomElement(['pcs', 'kg', 'lbs', 'box', 'pack', 'bottle']),
            'image' => null, // Will be set separately if needed
            'track_stock' => $this->faker->boolean(90), // 90% chance of tracking stock
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
            'store_id' => Store::factory(),
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Indicate that the product has low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(1, 5),
            'min_stock_level' => 10,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product has an image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'products/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Indicate that the product doesn't track stock.
     */
    public function noStockTracking(): static
    {
        return $this->state(fn (array $attributes) => [
            'track_stock' => false,
            'stock_quantity' => 0,
            'min_stock_level' => 0,
        ]);
    }

    /**
     * Create a high-value product.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'purchase_price' => $this->faker->randomFloat(2, 200, 1000),
            'selling_price' => $this->faker->randomFloat(2, 300, 1500),
        ]);
    }

    /**
     * Create a budget product.
     */
    public function budget(): static
    {
        return $this->state(fn (array $attributes) => [
            'purchase_price' => $this->faker->randomFloat(2, 1, 20),
            'selling_price' => $this->faker->randomFloat(2, 2, 30),
        ]);
    }
} 