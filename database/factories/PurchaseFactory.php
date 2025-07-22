<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Store;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $taxRate = $this->faker->randomFloat(2, 0, 0.15); // 0-15% tax
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = $this->faker->randomFloat(2, 0, $subtotal * 0.1); // Up to 10% discount
        $totalAmount = $subtotal + $taxAmount - $discountAmount;
        $amountPaid = $this->faker->randomFloat(2, 0, $totalAmount);
        $amountDue = $totalAmount - $amountPaid;

        return [
            'purchase_number' => 'PUR-' . $this->faker->unique()->numerify('######'),
            'supplier_id' => Supplier::factory(),
            'user_id' => User::factory(),
            'store_id' => Store::factory(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'bank_transfer', 'credit']),
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'order_date' => $this->faker->date(),
            'delivery_date' => $this->faker->optional()->date(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the purchase is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'amount_paid' => $attributes['total_amount'],
            'amount_due' => 0,
        ]);
    }

    /**
     * Indicate that the purchase is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the purchase is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the purchase is fully paid.
     */
    public function fullyPaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount_paid' => $attributes['total_amount'],
            'amount_due' => 0,
        ]);
    }

    /**
     * Indicate that the purchase is unpaid.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount_paid' => 0,
            'amount_due' => $attributes['total_amount'],
        ]);
    }

    /**
     * Indicate that the purchase is on credit.
     */
    public function onCredit(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'credit',
            'amount_paid' => 0,
            'amount_due' => $attributes['total_amount'],
        ]);
    }

    /**
     * Create a high-value purchase.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'subtotal' => $this->faker->randomFloat(2, 2000, 20000),
        ]);
    }
} 