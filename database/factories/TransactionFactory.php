<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    private ?Collection $users = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>  $this->getUser()->id,
            'amount' => $this->faker->randomFloat(2, -5000, 5000),
            'description' => $this->faker->randomElement([
                    'Grocery shopping',
                    'Salary deposit',
                    'Restaurant bill',
                    'Online purchase',
                    'Utility payment',
                    'ATM withdrawal',
                    'Transfer',
                    'Subscription payment',
                    'Refund',
                    'Investment'
                ]) . ' - ' . $this->faker->words(2, true),
            'accountType' => AccountType::random(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Indicate that the transaction is a deposit.
     */
    public function deposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'description' => 'Deposit - ' . $this->faker->words(2, true),
        ]);
    }

    /**
     * Indicate that the transaction is a withdrawal.
     */
    public function withdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, -5000, -100),
            'description' => 'Withdrawal - ' . $this->faker->words(2, true),
        ]);
    }

    /**
     * Indicate that the transaction is for a specific account type.
     */
    public function forAccountType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'accountType' => $type,
        ]);
    }

    public function getUser(): User
    {
        if (empty($this->users) || $this->users->isEmpty()) {
            $this->users = User::all();
        }

        if ($this->users->isEmpty()) {
            return User::factory()->create();
        }

        return $this->users->random();
    }
}
