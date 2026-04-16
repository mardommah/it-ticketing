<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketLogFactory extends Factory
{
    protected $model = TicketLog::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['status_change', 'assignment', 'comment']),
            'details' => $this->faker->sentence(),
        ];
    }
}
