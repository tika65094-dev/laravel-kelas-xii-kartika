<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $regularUser;
    protected Genre $genre;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        Role::firstOrCreate(['id' => 2], ['name' => 'admin']);

        $profile1 = Profile::create(['umur' => 35, 'bio' => 'Admin Bio', 'alamat' => 'HQ']);
        $this->adminUser = User::create([
            'name' => 'Admin Controller',
            'email' => 'admin.crud@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $profile1->id,
        ]);

        $profile2 = Profile::create(['umur' => 21, 'bio' => 'User Bio', 'alamat' => 'City']);
        $this->regularUser = User::create([
            'name' => 'Regular Member',
            'email' => 'regular.crud@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile2->id,
        ]);

        $this->genre = Genre::firstOrCreate(['name' => 'Sci-Fi']);
    }

    public function test_guest_is_redirected_away_from_all_admin_routes(): void
    {
        $adminUrls = [
            '/admin',
            '/admin/films',
            '/admin/films/create',
            '/admin/film-cast',
            '/admin/users',
            '/admin/comments',
        ];

        foreach ($adminUrls as $url) {
            $response = $this->get($url);
            $response->assertRedirect();
        }
    }

    public function test_regular_user_is_forbidden_from_all_admin_routes(): void
    {
        $adminUrls = [
            '/admin',
            '/admin/films',
            '/admin/films/create',
            '/admin/film-cast',
            '/admin/users',
            '/admin/comments',
        ];

        foreach ($adminUrls as $url) {
            $response = $this->actingAs($this->regularUser)->get($url);
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_view_films_list_and_search(): void
    {
        $film = Film::create([
            'title' => 'UniqueInterstellarSearchTest',
            'sinopsis' => 'Space travel to save humanity.',
            'year' => 2014,
            'poster' => 'images/interstellar.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.films.index', ['search' => 'UniqueInterstellarSearchTest']));

        $response->assertStatus(200);
        $response->assertSee('UniqueInterstellarSearchTest');
        $response->assertSee('Kelola Data Film');
    }

    public function test_admin_can_create_film_with_validation(): void
    {
        // Test validation failure
        $response = $this->actingAs($this->adminUser)->post(route('admin.films.store'), [
            'title' => '',
            'genre_id' => '',
            'year' => 'invalid-year',
            'poster' => '',
            'sinopsis' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'genre_id', 'year', 'poster', 'sinopsis']);

        // Test valid creation
        $validData = [
            'title' => 'Dune Part Two Test',
            'genre_id' => $this->genre->id,
            'year' => 2024,
            'poster' => 'images/dune2.jpg',
            'sinopsis' => 'Paul Atreides unites with Chani and the Fremen.',
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.films.store'), $validData);

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('films', [
            'title' => 'Dune Part Two Test',
            'year' => 2024,
        ]);
    }

    public function test_admin_can_edit_and_update_film(): void
    {
        $film = Film::create([
            'title' => 'Oppenheimer Old Title',
            'sinopsis' => 'Story of the atomic bomb.',
            'year' => 2023,
            'poster' => 'images/oppenheimer.jpg',
            'genre_id' => $this->genre->id,
        ]);

        // Access edit form
        $response = $this->actingAs($this->adminUser)->get(route('admin.films.edit', $film->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Film: Oppenheimer Old Title');

        // Submit update
        $response = $this->actingAs($this->adminUser)->put(route('admin.films.update', $film->id), [
            'title' => 'Oppenheimer Updated Title',
            'genre_id' => $this->genre->id,
            'year' => 2023,
            'poster' => 'images/oppenheimer_new.jpg',
            'sinopsis' => 'Updated synopsis of the atomic bomb story.',
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'title' => 'Oppenheimer Updated Title',
        ]);
    }

    public function test_admin_can_delete_film_and_its_associated_perans_and_comments(): void
    {
        $film = Film::create([
            'title' => 'Film To Be Deleted',
            'sinopsis' => 'Sinopsis film hapus.',
            'year' => 2022,
            'poster' => 'images/delete.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $cast = Cast::create([
            'name' => 'Cast Deletion Test',
            'age' => 40,
            'bio' => 'Bio',
        ]);

        $peran = Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Supporting Actor',
        ]);

        $kritik = Kritik::create([
            'film_id' => $film->id,
            'user_id' => $this->adminUser->id,
            'comment' => 'Komentar pada film yang akan dihapus.',
            'rating' => 5,
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.films.destroy', $film->id));

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('films', ['id' => $film->id]);
        $this->assertDatabaseMissing('perans', ['id' => $peran->id]);
        $this->assertDatabaseMissing('kritiks', ['id' => $kritik->id]);
    }

    public function test_admin_can_view_users_and_delete_non_self_user(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Data Pengguna');
        $response->assertSee($this->regularUser->name);

        // Admin deleting another user
        $response = $this->actingAs($this->adminUser)->delete(route('admin.users.destroy', $this->regularUser->id));
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $this->regularUser->id]);

        // Admin trying to delete their own account -> should be prevented
        $response = $this->actingAs($this->adminUser)->delete(route('admin.users.destroy', $this->adminUser->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);
    }

    public function test_admin_can_view_and_delete_comment(): void
    {
        $film = Film::create([
            'title' => 'Film Comment Test',
            'sinopsis' => 'Sinopsis',
            'year' => 2021,
            'poster' => 'images/comment.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $kritik = Kritik::create([
            'film_id' => $film->id,
            'user_id' => $this->adminUser->id,
            'comment' => 'Komentar yang akan dihapus admin.',
            'rating' => 5,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.comments.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Data Komentar');
        $response->assertSee('Komentar yang akan dihapus admin.');

        // Delete comment
        $response = $this->actingAs($this->adminUser)->delete(route('admin.comments.destroy', $kritik->id));
        $response->assertRedirect(route('admin.comments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kritiks', ['id' => $kritik->id]);
    }
}

