<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Kritik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KritikController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, ?Film $film = null)
    {
        // Reject unauthenticated requests at backend level
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('home')->with('error', 'Silakan login terlebih dahulu untuk berkomentar.');
        }

        $filmId = ($film && $film->exists) ? $film->id : $request->input('film_id');

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:500'],
            'rating'  => ['nullable', 'integer', 'min:1', 'max:5'],
        ], [
            'comment.required' => 'Isi komentar tidak boleh kosong.',
            'comment.max'      => 'Komentar maksimal 500 karakter.',
        ]);

        if (!$filmId || !Film::where('id', $filmId)->exists()) {
            return back()->with('error', 'Film tidak ditemukan.');
        }

        // Save comment strictly with current authenticated user ID, ignore any input user_id
        Kritik::create([
            'comment' => $validated['comment'],
            'rating'  => $request->input('rating', 5),
            'film_id' => $filmId,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('movies.show', $filmId)
            ->with('comment_success', 'Komentar Anda berhasil dikirim!');
    }
}
