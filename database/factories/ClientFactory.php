<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement([
                $this->faker->company,
                $this->faker->company . ' ' . $this->faker->randomElement(['LLC', 'Inc.', 'Corp.', 'Ltd.', 'Group', 'Solutions', 'Technologies']),
            ]),
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip' => $this->faker->postcode,
            'country' => $this->faker->randomElement([
                'United States', 'Canada', 'United Kingdom', 'Germany',
                'France', 'Australia', 'Netherlands'
            ]),
            'tax_number' => $this->faker->numerify('##-#######'),
        ];
    }

    public function forUser(User $user):static
    {
        return $this->state(fn (array $attributes)=>[
            'user_id' => $user->id,
        ]);
    }
    public function withoutTaxNumber():static
    {
        return $this->state(fn (array $attributes)=>[
            'tax_number' => null,
        ]);
    }
}
