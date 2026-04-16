<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_is_accessible()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_dashboard_shows_correct_counts()
    {
        $user = User::factory()->create();
        Ticket::factory()->create(['status' => 'open']);
        Ticket::factory()->create(['status' => 'resolved']);
        Ticket::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('statusCounts', function ($statusCounts) {
            return $statusCounts['open'] == 1 && $statusCounts['pending'] == 1 && $statusCounts['resolved'] == 1;
        });
    }
}
