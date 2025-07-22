<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Setting;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'value' => $this->faker->sentence(),
            'group' => $this->faker->randomElement(['general', 'business', 'inventory', 'features', 'security']),
        ];
    }

    /**
     * Create a business setting.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'business',
        ]);
    }

    /**
     * Create an inventory setting.
     */
    public function inventory(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'inventory',
        ]);
    }

    /**
     * Create a features setting.
     */
    public function features(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'features',
        ]);
    }

    /**
     * Create a security setting.
     */
    public function security(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => 'security',
        ]);
    }

    /**
     * Create a boolean setting.
     */
    public function boolean(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $this->faker->boolean() ? '1' : '0',
        ]);
    }

    /**
     * Create a numeric setting.
     */
    public function numeric(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => (string) $this->faker->randomFloat(2, 0, 100),
        ]);
    }
} 