<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        Role::firstOrCreate(['id' => 2], ['name' => 'admin']);
    }

    public function test_user_cannot_register_with_invalid_data(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password'], null, 'register');
        $this->assertGuest();
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        $profile = Profile::create(['umur' => 20, 'bio' => 'test', 'alamat' => 'test']);
        User::create([
            'name' => 'Existing User',
            'email' => 'duplicate@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email'], null, 'register');
        $this->assertGuest();
    }

    public function test_user_can_register_successfully_with_hashed_password_and_default_user_role(): void
    {
        $plainPassword = 'secretPassword123';

        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        $user = User::where('email', 'budi.santoso@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Budi Santoso', $user->name);
        $this->assertEquals('user', $user->role);
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials_without_disclosing_specific_field(): void
    {
        $response = $this->post('/login', [
            'login' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login'], null, 'login');
        $this->assertGuest();
    }

    public function test_regular_user_login_redirects_to_home(): void
    {
        $profile = Profile::create(['umur' => 22, 'bio' => 'test', 'alamat' => 'test']);
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/login', [
            'login' => 'regular@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_user_login_redirects_to_admin_dashboard(): void
    {
        $profile = Profile::create(['umur' => 30, 'bio' => 'admin', 'alamat' => 'office']);
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/login', [
            'login' => 'superadmin@example.com',
            'password' => 'admin12345',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $profile = Profile::create(['umur' => 22, 'bio' => 'test', 'alamat' => 'test']);
        $user = User::create([
            'name' => 'Regular Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $profile = Profile::create(['umur' => 30, 'bio' => 'admin', 'alamat' => 'office']);
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss@example.com',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_user_can_logout_successfully(): void
    {
        $profile = Profile::create(['umur' => 22, 'bio' => 'test', 'alamat' => 'test']);
        $user = User::create([
            'name' => 'Logout User',
            'email' => 'logout@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');
        $this->assertGuest();
    }
}
