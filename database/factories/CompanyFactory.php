<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
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
            'name' => $this->faker->company . ' ' . $this->faker->randomElement(['LLC', 'Inc.', 'Corp.', 'Ltd.']),
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip' => $this->faker->postcode,
            'country' => $this->faker->randomElement(['United States', 'Canada', 'United Kingdom']),
            'tax_number' => $this->faker->numerify('##-#######'),
            'logo'=> "https://robohash.org/103.143.0.158.png",
            'website' => $this->faker->domainName,
        ];
    }

    public function forUser(User $user):static
    {
        return $this->state(fn (array $attributes)=>[
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
