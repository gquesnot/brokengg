<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', \App\Http\Livewire\Index::class)->name('home');
Route::get('/summoner/{summonerId}', \App\Http\Livewire\BaseSummoner::class)->name(\App\Enums\TabEnum::MATCHES->value);
Route::get('/summoner/{summonerId}/versus/{otherSummonerId}', \App\Http\Livewire\BaseSummoner::class)->name(\App\Enums\TabEnum::VERSUS->value);
Route::get('/summoner/{summonerId}/champions', \App\Http\Livewire\BaseSummoner::class)->name(\App\Enums\TabEnum::CHAMPIONS->value);
Route::get('/summoner/{summonerId}/encounters', \App\Http\Livewire\BaseSummoner::class)->name(\App\Enums\TabEnum::ENCOUNTERS->value);
Route::get('/summoner/{summonerId}/live-game', \App\Http\Livewire\BaseSummoner::class)->name(\App\Enums\TabEnum::LIVE_GAME->value);
Route::get('/summoner/{summonerId}/match-detail/{matchId}', \App\Http\Livewire\BaseSummoner::class)->name(\App\Enums\TabEnum::MATCH_DETAIL->value);
Route::get('/sync', [\App\Http\Controllers\SyncLolController::class, 'dispatchLol'])->name('sync');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
