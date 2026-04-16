<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        // Assuming /dashboard is protected by auth middleware
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_login_with_env_credentials()
    {
        // Seed the admin user first
        $adminUser = 'admin';
        $adminPass = 'admin123';
        
        User::factory()->create([
            'username' => $adminUser,
            'password' => bcrypt($adminPass),
        ]);

        $response = $this->post('/login', [
            'username' => $adminUser,
            'password' => $adminPass,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_credentials()
    {
        User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
