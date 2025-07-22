<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Expense;
use App\Models\User;
use App\Models\Store;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'office_supplies', 'utilities', 'rent', 'marketing', 'equipment',
            'travel', 'meals', 'professional_services', 'insurance', 'maintenance', 'other'
        ];

        $categoryTitles = [
            'office_supplies' => ['Office Supplies', 'Stationery', 'Printer Ink', 'Paper'],
            'utilities' => ['Electricity Bill', 'Water Bill', 'Internet Bill', 'Phone Bill'],
            'rent' => ['Store Rent', 'Warehouse Rent', 'Office Rent'],
            'marketing' => ['Social Media Ads', 'Print Advertising', 'Promotional Materials'],
            'equipment' => ['Cash Register', 'Computer', 'Printer', 'Scanner'],
            'travel' => ['Business Travel', 'Fuel', 'Public Transport', 'Taxi'],
            'meals' => ['Team Lunch', 'Client Dinner', 'Coffee Meeting'],
            'professional_services' => ['Legal Services', 'Accounting', 'Consulting'],
            'insurance' => ['Business Insurance', 'Equipment Insurance', 'Liability Insurance'],
            'maintenance' => ['Equipment Repair', 'Store Maintenance', 'Cleaning'],
            'other' => ['Miscellaneous', 'Bank Charges', 'Licenses', 'Permits']
        ];

        $category = $this->faker->randomElement($categories);
        $title = $this->faker->randomElement($categoryTitles[$category]);

        return [
            'title' => $title,
            'description' => $this->faker->sentence(8),
            'amount' => $this->faker->randomFloat(2, 5, 1000),
            'category' => $category,
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'bank_transfer', 'mobile_money']),
            'expense_date' => $this->faker->date(),
            'receipt_image' => $this->faker->optional()->randomElement(['receipts/receipt1.jpg', 'receipts/receipt2.jpg']),
            'user_id' => User::factory(),
            'store_id' => Store::factory(),
        ];
    }

    /**
     * Indicate that the expense is for office supplies.
     */
    public function officeSupplies(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'office_supplies',
            'title' => $this->faker->randomElement(['Office Supplies', 'Stationery', 'Printer Ink', 'Paper']),
        ]);
    }

    /**
     * Indicate that the expense is for utilities.
     */
    public function utilities(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'utilities',
            'title' => $this->faker->randomElement(['Electricity Bill', 'Water Bill', 'Internet Bill', 'Phone Bill']),
        ]);
    }

    /**
     * Indicate that the expense is for rent.
     */
    public function rent(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'rent',
            'title' => $this->faker->randomElement(['Store Rent', 'Warehouse Rent', 'Office Rent']),
            'amount' => $this->faker->randomFloat(2, 500, 5000),
        ]);
    }

    /**
     * Indicate that the expense has a receipt image.
     */
    public function withReceipt(): static
    {
        return $this->state(fn (array $attributes) => [
            'receipt_image' => 'receipts/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a high-value expense.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
        ]);
    }

    /**
     * Create a small expense.
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, 1, 50),
        ]);
    }
} 