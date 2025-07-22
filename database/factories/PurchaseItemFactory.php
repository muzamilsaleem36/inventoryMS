<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 100);
        $unitCost = $this->faker->randomFloat(2, 1, 100);
        $discountAmount = $this->faker->randomFloat(2, 0, $unitCost * $quantity * 0.15); // Up to 15% discount
        $totalCost = ($unitCost * $quantity) - $discountAmount;

        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'discount_amount' => $discountAmount,
            'total_cost' => $totalCost,
        ];
    }

    /**
     * Indicate that the purchase item has no discount.
     */
    public function noDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_amount' => 0,
            'total_cost' => $attributes['unit_cost'] * $attributes['quantity'],
        ]);
    }

    /**
     * Indicate that the purchase item has a high discount.
     */
    public function highDiscount(): static
    {
        return $this->state(function (array $attributes) {
            $discountAmount = ($attributes['unit_cost'] * $attributes['quantity']) * 0.25; // 25% discount
            return [
                'discount_amount' => $discountAmount,
                'total_cost' => ($attributes['unit_cost'] * $attributes['quantity']) - $discountAmount,
            ];
        });
    }

    /**
     * Create a bulk purchase item.
     */
    public function bulk(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $this->faker->numberBetween(50, 500),
        ]);
    }

    /**
     * Create a high-value purchase item.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_cost' => $this->faker->randomFloat(2, 50, 500),
        ]);
    }
} 