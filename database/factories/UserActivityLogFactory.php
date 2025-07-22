<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserActivityLog;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserActivityLog>
 */
class UserActivityLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserActivityLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = [
            'login', 'logout', 'created', 'updated', 'deleted', 'viewed',
            'exported', 'imported', 'approved', 'rejected', 'cancelled'
        ];

        $modelTypes = [
            'App\Models\Product', 'App\Models\Customer', 'App\Models\Sale',
            'App\Models\Purchase', 'App\Models\User', 'App\Models\Category',
            'App\Models\Supplier', 'App\Models\Expense', 'App\Models\Setting'
        ];

        $action = $this->faker->randomElement($actions);
        $modelType = $this->faker->optional(70)->randomElement($modelTypes);

        return [
            'user_id' => User::factory(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelType ? $this->faker->numberBetween(1, 1000) : null,
            'description' => $this->faker->sentence(6),
            'old_values' => $this->faker->optional()->randomElement([
                ['name' => 'Old Product Name', 'price' => 10.00],
                ['email' => 'old@example.com', 'phone' => '123-456-7890'],
                ['status' => 'pending', 'total' => 100.00],
            ]),
            'new_values' => $this->faker->optional()->randomElement([
                ['name' => 'New Product Name', 'price' => 15.00],
                ['email' => 'new@example.com', 'phone' => '098-765-4321'],
                ['status' => 'completed', 'total' => 120.00],
            ]),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }

    /**
     * Indicate that the log is for login action.
     */
    public function login(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'login',
            'description' => 'User logged in',
            'model_type' => null,
            'model_id' => null,
            'old_values' => null,
            'new_values' => null,
        ]);
    }

    /**
     * Indicate that the log is for logout action.
     */
    public function logout(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'logout',
            'description' => 'User logged out',
            'model_type' => null,
            'model_id' => null,
            'old_values' => null,
            'new_values' => null,
        ]);
    }

    /**
     * Indicate that the log is for creation action.
     */
    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'created',
            'description' => 'Created a new record',
            'old_values' => null,
        ]);
    }

    /**
     * Indicate that the log is for update action.
     */
    public function updated(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'updated',
            'description' => 'Updated existing record',
        ]);
    }

    /**
     * Indicate that the log is for deletion action.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'deleted',
            'description' => 'Deleted a record',
            'new_values' => null,
        ]);
    }

    /**
     * Indicate that the log is for viewing action.
     */
    public function viewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'viewed',
            'description' => 'Viewed a record',
            'old_values' => null,
            'new_values' => null,
        ]);
    }
} 