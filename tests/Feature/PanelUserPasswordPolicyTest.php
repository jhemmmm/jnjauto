<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PanelUserPasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_created_user_must_have_a_strong_password(): void
    {
        $this->seedRoles();
        $admin = User::factory()->create(['role_id' => 2]);

        $response = $this
            ->actingAs($admin)
            ->postJson('/panel/api/users', [
                'name' => 'Staff User',
                'email' => 'staff.user@gmail.com',
                'password' => 'password',
                'role_id' => 3,
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_admin_can_create_user_with_a_strong_password(): void
    {
        $this->seedRoles();
        $admin = User::factory()->create(['role_id' => 2]);

        $response = $this
            ->actingAs($admin)
            ->postJson('/panel/api/users', [
                'name' => 'Staff User',
                'email' => 'staff.user@gmail.com',
                'password' => 'StaffPass1!',
                'role_id' => 3,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'name' => 'Staff User',
            'email' => 'staff.user@gmail.com',
            'role_id' => 3,
        ]);
    }

    public function test_admin_user_update_rejects_a_weak_new_password(): void
    {
        $this->seedRoles();
        $admin = User::factory()->create(['role_id' => 2]);
        $staff = User::factory()->create(['role_id' => 3]);

        $response = $this
            ->actingAs($admin)
            ->putJson("/panel/api/users/{$staff->id}", [
                'name' => $staff->name,
                'email' => $staff->email,
                'role_id' => 3,
                'password' => 'new-password',
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    private function seedRoles(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Customer', 'description' => 'Customer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Admin', 'description' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Staff', 'description' => 'Staff', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
