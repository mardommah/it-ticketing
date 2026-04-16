<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tickets()
    {
        $user = User::factory()->create();
        Ticket::factory()->count(5)->create();

        $response = $this->actingAs($user)->get(route('tickets.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tickets');
    }

    public function test_can_show_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));

        $response->assertStatus(200);
        $response->assertViewIs('tickets.show');
        $response->assertSee($ticket->subject);
    }

    public function test_can_update_ticket_status()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['status' => 'open']);

        $response = $this->actingAs($user)->put(route('tickets.update', $ticket), [
            'status' => 'resolved',
        ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertEquals('resolved', $ticket->fresh()->status);
        
        // Assert log was created
        $this->assertDatabaseHas('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'status_change',
        ]);
    }

    public function test_can_assign_ticket_to_user()
    {
        $admin = User::factory()->create();
        $staff = User::factory()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => null]);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'status' => 'open',
            'assigned_to' => $staff->id,
        ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertEquals($staff->id, $ticket->fresh()->assigned_to);
    }

    public function test_can_delete_ticket()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($user)->delete(route('tickets.destroy', $ticket));

        $response->assertRedirect(route('tickets.index'));
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }
}
