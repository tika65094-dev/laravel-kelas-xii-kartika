<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Peran;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FilmCastTest extends TestCase
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

        $profile1 = Profile::create(['umur' => 30, 'bio' => 'Admin Bio', 'alamat' => 'Office']);
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin.filmcast@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $profile1->id,
        ]);

        $profile2 = Profile::create(['umur' => 20, 'bio' => 'User Bio', 'alamat' => 'Home']);
        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user.filmcast@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile2->id,
        ]);

        $this->genre = Genre::firstOrCreate(['name' => 'Action']);
    }

    public function test_movie_show_displays_cast_name_and_character_role(): void
    {
        $film = Film::create([
            'title' => 'Inception Test',
            'sinopsis' => 'A thief who steals corporate secrets through dream-sharing technology.',
            'year' => 2010,
            'poster' => 'test/poster.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $cast = Cast::create([
            'name' => 'Leonardo DiCaprio',
            'age' => 48,
            'bio' => 'American actor and film producer.',
        ]);

        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Dom Cobb',
        ]);

        $response = $this->get(route('movies.show', $film->id));

        $response->assertStatus(200);
        $response->assertSee('Leonardo DiCaprio');
        $response->assertSee('Dom Cobb');
    }

    public function test_movie_show_displays_placeholder_when_film_has_no_cast(): void
    {
        $film = Film::create([
            'title' => 'Empty Cast Movie',
            'sinopsis' => 'A film without any registered casts yet.',
            'year' => 2024,
            'poster' => 'test/empty.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $response = $this->get(route('movies.show', $film->id));

        $response->assertStatus(200);
        $response->assertSee('Data cast belum tersedia');
    }

    public function test_regular_user_cannot_access_admin_film_cast(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.film-cast'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_film_cast_management_page(): void
    {
        $film = Film::create([
            'title' => 'Admin Film View',
            'sinopsis' => 'Sinopsis',
            'year' => 2023,
            'poster' => 'test/admin.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.film-cast', ['film_id' => $film->id]));

        $response->assertStatus(200);
        $response->assertSee('Kelola Cast Film');
        $response->assertSee('Admin Film View');
    }

    public function test_admin_can_add_cast_to_film(): void
    {
        $film = Film::create([
            'title' => 'Film Add Cast',
            'sinopsis' => 'Sinopsis film',
            'year' => 2022,
            'poster' => 'test/poster.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $cast = Cast::create([
            'name' => 'Robert Downey Jr.',
            'age' => 58,
            'bio' => 'American actor.',
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.film-cast.store'), [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Tony Stark / Iron Man',
        ]);

        $response->assertRedirect(route('admin.film-cast', ['film_id' => $film->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('perans', [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Tony Stark / Iron Man',
        ]);
    }

    public function test_admin_cannot_add_duplicate_cast_to_same_film(): void
    {
        $film = Film::create([
            'title' => 'Film Duplicate Cast',
            'sinopsis' => 'Sinopsis',
            'year' => 2021,
            'poster' => 'test/poster.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $cast = Cast::create([
            'name' => 'Chris Evans',
            'age' => 42,
            'bio' => 'American actor.',
        ]);

        Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Steve Rogers',
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.film-cast.store'), [
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Captain America',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_admin_can_remove_cast_from_film(): void
    {
        $film = Film::create([
            'title' => 'Film Remove Cast',
            'sinopsis' => 'Sinopsis',
            'year' => 2020,
            'poster' => 'test/poster.jpg',
            'genre_id' => $this->genre->id,
        ]);

        $cast = Cast::create([
            'name' => 'Scarlett Johansson',
            'age' => 39,
            'bio' => 'American actress.',
        ]);

        $peran = Peran::create([
            'film_id' => $film->id,
            'cast_id' => $cast->id,
            'actor' => 'Natasha Romanoff',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.film-cast.destroy', $peran->id));

        $response->assertRedirect(route('admin.film-cast', ['film_id' => $film->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('perans', [
            'id' => $peran->id,
        ]);
    }
}

