<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FilmController,
    AuthController,
    AdminController,
    KritikController,
};

Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');

// Comment route (strictly authenticated)
Route::post('/movies/{film}/comments', [KritikController::class, 'store'])
    ->middleware('auth')
    ->name('movies.comments.store');

// Authentication routes
Route::get('/login', function () {
    return redirect()->route('home')->with('active_modal', 'login');
});
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Film CRUD
    Route::get('/films', [AdminController::class, 'films'])->name('admin.films.index');
    Route::get('/films/create', [AdminController::class, 'filmCreate'])->name('admin.films.create');
    Route::post('/films', [AdminController::class, 'filmStore'])->name('admin.films.store');
    Route::get('/films/{film}/edit', [AdminController::class, 'filmEdit'])->name('admin.films.edit');
    Route::put('/films/{film}', [AdminController::class, 'filmUpdate'])->name('admin.films.update');
    Route::delete('/films/{film}', [AdminController::class, 'filmDestroy'])->name('admin.films.destroy');

    // Cast Film Management
    Route::get('/film-cast', [AdminController::class, 'filmCast'])->name('admin.film-cast');
    Route::post('/film-cast', [AdminController::class, 'filmCastStore'])->name('admin.film-cast.store');
    Route::delete('/film-cast/{peran}', [AdminController::class, 'filmCastDestroy'])->name('admin.film-cast.destroy');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::delete('/users/{user}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');

    // Comment Management
    Route::get('/comments', [AdminController::class, 'comments'])->name('admin.comments.index');
    Route::delete('/comments/{comment}', [AdminController::class, 'commentDestroy'])->name('admin.comments.destroy');
});
