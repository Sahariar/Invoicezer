<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = Carbon::instance($issueDate)->addDays($this->faker->randomElement([15, 30, 45, 60]));
        return [
            //
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'company_id' => Company::factory(),
            'invoice_number' => 'INV-' . date('Y') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'status' => $this->getWeightedStatus($issueDate, $dueDate),
            'subtotal' => 0, // Will be calculated after items are created
            'tax_rate' => $this->faker->randomElement([0, 5, 6.25, 7.5, 8.25, 10, 12, 15]),
            'tax_amount' => 0,
            'discount_rate' => $this->faker->randomElement([0, 0, 0, 0, 5, 10, 15, 20]),
            'discount_amount' => 0,
            'total' => 0,
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'CAD', 'AUD']),
            'notes' => $this->faker->optional(0.7)->randomElement([
                'Thank you for your business! We appreciate the opportunity to work with you.',
                'Please contact us if you have any questions about this invoice.',
                'Payment terms: Net 30 days from invoice date.',
                'This invoice includes all agreed-upon services for the project.',
            ]),
            'terms' => $this->faker->randomElement([
                'Payment is due within 30 days of invoice date. Late payments may incur a 1.5% monthly service charge.',
                'Net 15 days. Please remit payment to the address shown above.',
                'Payment due upon receipt. Thank you for your prompt payment.',
                'Net 30 days. Payment can be made via check, ACH, or credit card.',
                'Payment terms: 2/10 Net 30. 2% discount if paid within 10 days.',
            ]),
            'paid_at' => null,
        ];
    }
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function forClient(Client $client): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => $client->id,
            'user_id' => $client->user_id,
        ]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $company->id,
            'user_id' => $company->user_id,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'paid_at' => null,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'paid_at' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = Carbon::parse($attributes['issue_date']);
            $paidAt = $issueDate->copy()->addDays($this->faker->numberBetween(1, 30));

            return [
                'status' => 'paid',
                'paid_at' => $paidAt,
            ];
        });
    }

    public function overdue(): static
    {
        $pastDate = $this->faker->dateTimeBetween('-6 months', '-1 month');
        $dueDate = Carbon::instance($pastDate)->addDays(30);

        return $this->state(fn (array $attributes) => [
            'issue_date' => $pastDate,
            'due_date' => $dueDate,
            'status' => 'overdue',
            'paid_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'paid_at' => null,
        ]);
    }

    private function getWeightedStatus($issueDate, $dueDate): string
    {
        $now = Carbon::now();
        $issueCarbon = Carbon::instance($issueDate);
        $dueCarbon = Carbon::instance($dueDate);

        // If due date has passed, likely overdue or paid
        if ($dueCarbon->isPast()) {
            return $this->faker->randomElement(['paid', 'paid', 'paid', 'overdue']);
        }

        // If issued recently, might still be draft or sent
        if ($issueCarbon->isAfter($now->subDays(7))) {
            return $this->faker->randomElement(['draft', 'sent', 'sent', 'paid']);
        }

        // Default weighted distribution
        return $this->faker->randomElement([
            'paid', 'paid', 'paid', 'paid', 'paid', 'paid',  // 60%
            'sent', 'sent',  // 20%
            'overdue',  // 10%
            'draft',  // 5%
            'cancelled'  // 5%
        ]);
    }
}
