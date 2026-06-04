<?php

namespace Database\Factories;

use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slot>
 */
class SlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement([
                '$table->integer('balance')->default(500000);',
                'Chuyến xe Limousine 14:00',
                'Slot Tập Gym Vé VIP 08:00',
                'Slot Tập Gym Vé VIP 17:00',
                'Vé Xem Phim Phòng Chiếu 03'
            ]),
            'max_seats' => $this->faker->randomElement([10, 15, 20, 30]),
            'booked_seats' => 0,
            'price' => $this->faker->randomElement([50000, 100000, 150000, 200000]),
        ];
    }
}
