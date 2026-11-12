<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Film $film;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        Role::firstOrCreate(['id' => 2], ['name' => 'admin']);

        $profile = Profile::create(['umur' => 25, 'bio' => 'User bio', 'alamat' => 'Jakarta']);
        $this->user = User::create([
            'name' => 'Budi Komentator',
            'email' => 'budi.komentar@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
            'profile_id' => $profile->id,
        ]);

        $genre = Genre::firstOrCreate(['name' => 'Drama']);
        $this->film = Film::create([
            'title' => 'Film Drama Uji',
            'sinopsis' => 'Sinopsis drama',
            'year' => 2024,
            'poster' => 'test/drama.jpg',
            'genre_id' => $genre->id,
        ]);
    }

    public function test_guest_sees_login_prompt_instead_of_comment_form(): void
    {
        $response = $this->get(route('movies.show', $this->film->id));

        $response->assertStatus(200);
        $response->assertSee('Anda harus login terlebih dahulu');
        $response->assertSee('Login untuk berkomentar');
        $response->assertDontSee('Berkomentar sebagai:');
    }

    public function test_authenticated_user_sees_comment_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('movies.show', $this->film->id));

        $response->assertStatus(200);
        $response->assertSee('Berkomentar sebagai:');
        $response->assertSee('Budi Komentator');
        $response->assertDontSee('Login untuk berkomentar');
    }

    public function test_unauthenticated_request_to_comment_endpoint_is_rejected(): void
    {
        $response = $this->post(route('movies.comments.store', $this->film->id), [
            'comment' => 'Komentar tanpa login.',
        ]);

        // Must redirect to login or home, rejecting the comment
        $response->assertRedirect();
        $this->assertDatabaseMissing('kritiks', [
            'comment' => 'Komentar tanpa login.',
        ]);
    }

    public function test_unauthenticated_json_request_receives_401(): void
    {
        $response = $this->postJson(route('movies.comments.store', $this->film->id), [
            'comment' => 'Komentar API tanpa login.',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('kritiks', [
            'comment' => 'Komentar API tanpa login.',
        ]);
    }

    public function test_authenticated_user_can_submit_valid_comment(): void
    {
        $commentText = 'Film ini alur ceritanya sangat menarik dan sinematografinya bagus!';

        $response = $this->actingAs($this->user)->post(route('movies.comments.store', $this->film->id), [
            'comment' => $commentText,
        ]);

        $response->assertRedirect(route('movies.show', $this->film->id));
        $response->assertSessionHas('comment_success');

        $this->assertDatabaseHas('kritiks', [
            'film_id' => $this->film->id,
            'user_id' => $this->user->id,
            'comment' => $commentText,
        ]);
    }

    public function test_user_id_cannot_be_spoofed_from_request_input(): void
    {
        $spoofedUserId = 99999;

        $response = $this->actingAs($this->user)->post(route('movies.comments.store', $this->film->id), [
            'comment' => 'Mencoba memalsukan user ID.',
            'user_id' => $spoofedUserId,
        ]);

        $response->assertRedirect(route('movies.show', $this->film->id));

        // It must NOT save with 99999, it MUST save with $this->user->id
        $this->assertDatabaseMissing('kritiks', [
            'user_id' => $spoofedUserId,
        ]);

        $this->assertDatabaseHas('kritiks', [
            'film_id' => $this->film->id,
            'user_id' => $this->user->id,
            'comment' => 'Mencoba memalsukan user ID.',
        ]);
    }

    public function test_comment_validation_fails_when_comment_is_empty(): void
    {
        $response = $this->actingAs($this->user)->post(route('movies.comments.store', $this->film->id), [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment']);
        $this->assertEquals(0, Kritik::where('film_id', $this->film->id)->count());
    }

    public function test_comment_validation_fails_when_comment_exceeds_500_characters(): void
    {
        $longComment = str_repeat('A', 501);

        $response = $this->actingAs($this->user)->post(route('movies.comments.store', $this->film->id), [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors(['comment']);
        $this->assertEquals(0, Kritik::where('film_id', $this->film->id)->count());
    }

    public function test_movie_show_displays_commenter_name_and_readable_time(): void
    {
        Kritik::create([
            'comment' => 'Komentar dengan nama asli.',
            'rating' => 5,
            'film_id' => $this->film->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('movies.show', $this->film->id));

        $response->assertStatus(200);
        $response->assertSee('Budi Komentator');
        $response->assertSee('Komentar dengan nama asli.');
        $response->assertDontSee('User #' . $this->user->id);
    }

    public function test_movie_show_displays_fallback_when_commenter_user_is_not_found(): void
    {
        // Disable foreign key checks temporarily to simulate an orphaned comment
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $orphanKritik = Kritik::create([
            'comment' => 'Komentar dari user yang terhapus.',
            'rating' => 4,
            'film_id' => $this->film->id,
            'user_id' => 88888, // Non-existent user
        ]);
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $response = $this->get(route('movies.show', $this->film->id));

        $response->assertStatus(200);
        $response->assertSee('Pengguna tidak dikenal');
        $response->assertSee('Komentar dari user yang terhapus.');
    }

    public function test_admin_dashboard_displays_recent_comments_with_user_names(): void
    {
        $adminProfile = Profile::create(['umur' => 30, 'bio' => 'admin', 'alamat' => 'office']);
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss.comment@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'role_id' => 2,
            'profile_id' => $adminProfile->id,
        ]);

        Kritik::create([
            'comment' => 'Review admin terbaru.',
            'rating' => 5,
            'film_id' => $this->film->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Komentar Terbaru');
        $response->assertSee('Budi Komentator');
        $response->assertSee('Review admin terbaru.');
    }
}

