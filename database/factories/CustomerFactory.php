<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'credit_limit' => $this->faker->randomFloat(2, 0, 5000),
            'current_balance' => $this->faker->randomFloat(2, 0, 1000),
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
        ];
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the customer has no email.
     */
    public function noEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }

    /**
     * Indicate that the customer has a high credit limit.
     */
    public function highCredit(): static
    {
        return $this->state(fn (array $attributes) => [
            'credit_limit' => $this->faker->randomFloat(2, 5000, 20000),
        ]);
    }

    /**
     * Indicate that the customer has outstanding balance.
     */
    public function withBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_balance' => $this->faker->randomFloat(2, 100, 2000),
        ]);
    }

    /**
     * Indicate that the customer is a minor.
     */
    public function minor(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => $this->faker->date('Y-m-d', '-10 years'),
        ]);
    }

    /**
     * Indicate that the customer is a senior.
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => $this->faker->date('Y-m-d', '-70 years'),
        ]);
    }
} 