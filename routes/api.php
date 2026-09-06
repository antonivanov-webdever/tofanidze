<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public read API — the portfolio's content, exposed as JSON.
|--------------------------------------------------------------------------
|
| Prefixed with /api automatically. Versioned under /v1 from the start so a
| breaking change later doesn't have to disturb whatever links to this.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

    Route::get('skills', [SkillController::class, 'index'])->name('skills.index');

    // First-party page-view beacon — no third-party analytics script, no
    // cookies, no fingerprinting. Fired once per page load from resources/js/app.js.
    //
    // OPTIONS, not POST: see the matching comment on the /contact route in
    // routes/web.php for why — same CDN limitation, same fix.
    Route::options('events', [EventController::class, 'store'])
        ->middleware('throttle:events')
        ->name('events.store');
});
