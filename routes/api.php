<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MangaController;
use App\Http\Controllers\Api\ChapterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Manga routes
    Route::get('/mangas', [MangaController::class, 'index']);
    Route::get('/mangas/featured', [MangaController::class, 'featured']);
    Route::get('/mangas/popular', [MangaController::class, 'popular']);
    Route::get('/mangas/latest', [MangaController::class, 'latest']);
    Route::get('/mangas/search', [MangaController::class, 'search']);
    Route::get('/mangas/category/{categoryId}', [MangaController::class, 'byCategory']);
    Route::get('/mangas/{slug}', [MangaController::class, 'show']);

    // Chapter routes
    Route::get('/mangas/{mangaId}/chapters', [ChapterController::class, 'index']);
    Route::get('/chapters/latest', [ChapterController::class, 'latest']);
    Route::get('/chapters/{slug}', [ChapterController::class, 'show']);
    Route::get('/mangas/{mangaId}/chapters/{chapterNumber}/next', [ChapterController::class, 'next']);
    Route::get('/mangas/{mangaId}/chapters/{chapterNumber}/previous', [ChapterController::class, 'previous']);
});

// Protected routes
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // User routes
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'updateMe']);

    // Admin routes
    Route::middleware(['check.role:admin,mod'])->group(function () {
        // Manga management
        Route::post('/mangas', [MangaController::class, 'store']);
        Route::put('/mangas/{slug}', [MangaController::class, 'update']);
        Route::delete('/mangas/{slug}', [MangaController::class, 'destroy']);

        // Chapter management
        Route::post('/mangas/{mangaId}/chapters', [ChapterController::class, 'store']);
        Route::put('/chapters/{slug}', [ChapterController::class, 'update']);
        Route::delete('/chapters/{slug}', [ChapterController::class, 'destroy']);
    });

    // Admin-only routes
    Route::middleware(['check.role:admin'])->group(function () {
        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{username}', [UserController::class, 'show']);
        Route::put('/users/{username}', [UserController::class, 'update']);
        Route::delete('/users/{username}', [UserController::class, 'destroy']);
        Route::get('/users/role/{role}', [UserController::class, 'byRole']);
    });
});
