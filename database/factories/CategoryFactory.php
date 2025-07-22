<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryName = $this->faker->unique()->randomElement([
            'Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports',
            'Food & Beverages', 'Beauty & Health', 'Toys & Games', 'Automotive',
            'Office Supplies', 'Pet Supplies', 'Musical Instruments', 'Tools',
            'Jewelry', 'Furniture', 'Art & Crafts', 'Outdoor Equipment'
        ]);

        return [
            'name' => $categoryName,
            'code' => strtoupper($this->faker->unique()->bothify('CAT###')),
            'description' => $this->faker->sentence(10),
            'image' => null, // Will be set separately if needed
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category has an image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'categories/' . $this->faker->uuid() . '.jpg',
        ]);
    }
} 