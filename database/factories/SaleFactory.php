<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use App\Models\Store;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 1000);
        $taxRate = $this->faker->randomFloat(2, 0, 0.15); // 0-15% tax
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = $this->faker->randomFloat(2, 0, $subtotal * 0.2); // Up to 20% discount
        $totalAmount = $subtotal + $taxAmount - $discountAmount;
        $amountPaid = $totalAmount + $this->faker->randomFloat(2, 0, 50); // May include tip or overpayment
        $changeAmount = max(0, $amountPaid - $totalAmount);

        return [
            'sale_number' => 'SALE-' . $this->faker->unique()->numerify('######'),
            'customer_id' => $this->faker->boolean(70) ? Customer::factory() : null, // 70% chance of having customer
            'user_id' => User::factory(),
            'store_id' => Store::factory(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'mobile_money', 'bank_transfer', 'credit']),
            'amount_paid' => $amountPaid,
            'change_amount' => $changeAmount,
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled', 'refunded']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the sale is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the sale is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the sale is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the sale is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
        ]);
    }

    /**
     * Indicate that the sale is cash payment.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
        ]);
    }

    /**
     * Indicate that the sale is card payment.
     */
    public function card(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'card',
            'change_amount' => 0, // No change for card payments
        ]);
    }

    /**
     * Indicate that the sale has no customer (walk-in).
     */
    public function walkIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => null,
        ]);
    }

    /**
     * Create a high-value sale.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'subtotal' => $this->faker->randomFloat(2, 500, 5000),
        ]);
    }
} 