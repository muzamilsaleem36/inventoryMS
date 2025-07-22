<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleItem>
 */
class SaleItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unitPrice = $this->faker->randomFloat(2, 1, 200);
        $discountAmount = $this->faker->randomFloat(2, 0, $unitPrice * $quantity * 0.2); // Up to 20% discount
        $totalPrice = ($unitPrice * $quantity) - $discountAmount;

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $discountAmount,
            'total_price' => $totalPrice,
        ];
    }

    /**
     * Indicate that the sale item has no discount.
     */
    public function noDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_amount' => 0,
            'total_price' => $attributes['unit_price'] * $attributes['quantity'],
        ]);
    }

    /**
     * Indicate that the sale item has a high discount.
     */
    public function highDiscount(): static
    {
        return $this->state(function (array $attributes) {
            $discountAmount = ($attributes['unit_price'] * $attributes['quantity']) * 0.3; // 30% discount
            return [
                'discount_amount' => $discountAmount,
                'total_price' => ($attributes['unit_price'] * $attributes['quantity']) - $discountAmount,
            ];
        });
    }

    /**
     * Create a bulk sale item.
     */
    public function bulk(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $this->faker->numberBetween(10, 100),
        ]);
    }

    /**
     * Create a high-value sale item.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_price' => $this->faker->randomFloat(2, 100, 1000),
        ]);
    }
} 