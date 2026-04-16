<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'whatsapp_id' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'from' => $this->faker->phoneNumber(),
            'participant' => $this->faker->phoneNumber(),
            'reporter_name' => $this->faker->name(),
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['open', 'pending', 'resolved']),
            'assigned_to' => null,
            'category' => $this->faker->word(),
            'whatsapp_timestamp' => now()->timestamp,
        ];
    }
}
