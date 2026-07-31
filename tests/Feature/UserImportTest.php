<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_user_import_template(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('users.import-template'));

        $response->assertOk();
        $response->assertDownload('template-import-users.csv');
        $this->assertStringContainsString(
            'name,username,email,password,role,is_active',
            $response->streamedContent()
        );
    }

    public function test_admin_can_import_users_with_password_from_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $csv = implode("\n", [
            'name,username,email,password,role,is_active',
            'Petugas IT,petugas,it@example.com,rahasia123,user,1',
        ]);

        $response = $this->actingAs($admin)->post(route('users.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent('users.csv', $csv),
        ]);

        $response->assertRedirect(route('users.index'));

        $user = User::where('username', 'petugas')->firstOrFail();
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertTrue($user->is_active);
    }
}
