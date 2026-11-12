<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use App\Models\Cast;
use App\Models\Peran;
use App\Models\Profile;
use App\Models\Kritik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $totalFilms = Film::count();
        $totalGenres = Genre::count();
        $totalUsers = User::count();
        $totalCasts = Cast::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentFilms = Film::with('genre')->latest()->take(5)->get();
        $recentComments = Kritik::with(['user', 'film'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalFilms',
            'totalGenres',
            'totalUsers',
            'totalCasts',
            'recentUsers',
            'recentFilms',
            'recentComments'
        ));
    }

    /**
     * Display list of films with search and pagination.
     */
    public function films(Request $request)
    {
        $query = Film::with(['genre', 'perans'])->withCount('perans');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%")
                  ->orWhereHas('genre', function ($g) use ($search) {
                      $g->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $films = $query->orderBy('id', 'desc')->paginate(15);

        return view('admin.films.index', compact('films'));
    }

    /**
     * Show form to create a new film.
     */
    public function filmCreate()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.films.create', compact('genres'));
    }

    /**
     * Store a newly created film in storage.
     */
    public function filmStore(Request $request)
    {
        $currentYear = date('Y') + 5;
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'genre_id' => ['required', 'exists:genres,id'],
            'year'     => ['required', 'integer', 'min:1900', "max:{$currentYear}"],
            'poster'   => ['required', 'string', 'max:255'],
            'sinopsis' => ['required', 'string'],
        ], [
            'title.required'    => 'Judul film wajib diisi.',
            'genre_id.required' => 'Genre film wajib dipilih.',
            'genre_id.exists'   => 'Genre yang dipilih tidak valid.',
            'year.required'     => 'Tahun rilis wajib diisi.',
            'year.min'          => 'Tahun rilis minimal 1900.',
            'year.max'          => "Tahun rilis maksimal {$currentYear}.",
            'poster.required'   => 'Poster film wajib diisi.',
            'sinopsis.required' => 'Sinopsis film wajib diisi.',
        ]);

        $film = Film::create($validated);

        return redirect()
            ->route('admin.films.index')
            ->with('success', "Film \"{$film->title}\" berhasil ditambahkan.");
    }

    /**
     * Show form to edit an existing film.
     */
    public function filmEdit(Film $film)
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.films.edit', compact('film', 'genres'));
    }

    /**
     * Update the specified film in storage.
     */
    public function filmUpdate(Request $request, Film $film)
    {
        $currentYear = date('Y') + 5;
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'genre_id' => ['required', 'exists:genres,id'],
            'year'     => ['required', 'integer', 'min:1900', "max:{$currentYear}"],
            'poster'   => ['required', 'string', 'max:255'],
            'sinopsis' => ['required', 'string'],
        ], [
            'title.required'    => 'Judul film wajib diisi.',
            'genre_id.required' => 'Genre film wajib dipilih.',
            'genre_id.exists'   => 'Genre yang dipilih tidak valid.',
            'year.required'     => 'Tahun rilis wajib diisi.',
            'poster.required'   => 'Poster film wajib diisi.',
            'sinopsis.required' => 'Sinopsis film wajib diisi.',
        ]);

        $film->update($validated);

        return redirect()
            ->route('admin.films.index')
            ->with('success', "Film \"{$film->title}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified film from storage.
     */
    public function filmDestroy(Film $film)
    {
        $title = $film->title;

        DB::transaction(function () use ($film) {
            $film->perans()->delete();
            $film->kritiks()->delete();
            $film->delete();
        });

        return redirect()
            ->route('admin.films.index')
            ->with('success', "Film \"{$title}\" beserta peran dan komentar terkait berhasil dihapus.");
    }

    /**
     * Display list of users with search and pagination.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Delete a user account from storage.
     */
    public function userDestroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.');
        }

        $userName = $user->name;

        DB::transaction(function () use ($user) {
            Kritik::where('user_id', $user->id)->delete();
            $profileId = $user->profile_id;
            $user->delete();
            if ($profileId) {
                Profile::where('id', $profileId)->delete();
            }
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna \"{$userName}\" berhasil dihapus.");
    }

    /**
     * Display list of comments with search and pagination.
     */
    public function comments(Request $request)
    {
        $query = Kritik::with(['user', 'film']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('film', function ($f) use ($search) {
                      $f->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $comments = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Delete a comment from storage.
     */
    public function commentDestroy(Kritik $comment)
    {
        $comment->delete();

        return redirect()
            ->route('admin.comments.index')
            ->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Display the film-cast management page.
     */
    public function filmCast(Request $request)
    {
        $films = Film::orderBy('title')->get();
        $selectedFilm = null;
        $filmPerans = collect();
        $availableCasts = collect();

        if ($request->filled('film_id')) {
            $selectedFilm = Film::find($request->film_id);

            if ($selectedFilm) {
                $filmPerans = Peran::with('cast')
                    ->where('film_id', $selectedFilm->id)
                    ->get();

                // Get casts not already assigned to this film
                $assignedCastIds = $filmPerans->pluck('cast_id')->toArray();
                $availableCasts = Cast::whereNotIn('id', $assignedCastIds)
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('admin.film-cast', compact(
            'films',
            'selectedFilm',
            'filmPerans',
            'availableCasts'
        ));
    }

    /**
     * Store a new cast assignment to a film.
     */
    public function filmCastStore(Request $request)
    {
        $request->validate([
            'film_id' => ['required', 'exists:films,id'],
            'cast_id' => ['required', 'exists:casts,id'],
            'actor'   => ['required', 'string', 'max:255'],
        ], [
            'film_id.required' => 'Film wajib dipilih.',
            'cast_id.required' => 'Cast wajib dipilih.',
            'cast_id.exists'   => 'Cast tidak ditemukan.',
            'actor.required'   => 'Nama peran/karakter wajib diisi.',
        ]);

        // Check for duplicate assignment
        $exists = Peran::where('film_id', $request->film_id)
            ->where('cast_id', $request->cast_id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Cast ini sudah ditambahkan ke film tersebut.');
        }

        Peran::create([
            'film_id' => $request->film_id,
            'cast_id' => $request->cast_id,
            'actor'   => $request->actor,
        ]);

        return redirect()
            ->route('admin.film-cast', ['film_id' => $request->film_id])
            ->with('success', 'Cast berhasil ditambahkan ke film.');
    }

    /**
     * Remove a cast assignment from a film.
     */
    public function filmCastDestroy(Peran $peran)
    {
        $filmId = $peran->film_id;
        $peran->delete();

        return redirect()
            ->route('admin.film-cast', ['film_id' => $filmId])
            ->with('success', 'Cast berhasil dihapus dari film.');
    }
}
