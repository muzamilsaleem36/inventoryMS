<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company_name' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'tax_number' => $this->faker->numerify('TAX-########'),
            'credit_limit' => $this->faker->randomFloat(2, 5000, 50000),
            'current_balance' => $this->faker->randomFloat(2, 0, 10000),
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
        ];
    }

    /**
     * Indicate that the supplier is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the supplier has no email.
     */
    public function noEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }

    /**
     * Indicate that the supplier has no tax number.
     */
    public function noTaxNumber(): static
    {
        return $this->state(fn (array $attributes) => [
            'tax_number' => null,
        ]);
    }

    /**
     * Indicate that the supplier has a high credit limit.
     */
    public function highCredit(): static
    {
        return $this->state(fn (array $attributes) => [
            'credit_limit' => $this->faker->randomFloat(2, 50000, 200000),
        ]);
    }

    /**
     * Indicate that the supplier has outstanding balance.
     */
    public function withBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_balance' => $this->faker->randomFloat(2, 1000, 25000),
        ]);
    }

    /**
     * Indicate that the supplier is a major supplier.
     */
    public function major(): static
    {
        return $this->state(fn (array $attributes) => [
            'credit_limit' => $this->faker->randomFloat(2, 100000, 500000),
            'company_name' => $this->faker->company() . ' Corporation',
        ]);
    }
} 