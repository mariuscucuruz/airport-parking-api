<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'date_start' => fake()->dateTimeBetween('+1 day', '+1 year')->format('Y-m-d'),
            'date_end'   => fake()->dateTimeBetween('+1 day', '+1 year')->format('Y-m-d'),
            'email'      => fake()->safeEmail(),
        ];
    }
}
