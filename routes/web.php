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
Route::get('/summoner/{summonerId}', \App\Http\Livewire\BaseSummoner::class)->name('summoner');
Route::get('/summoner/{summonerId}/versus/{otherSummonerId}', \App\Http\Livewire\BaseSummoner::class)->name('versus');
Route::get('/summoner/{summonerId}/champions', \App\Http\Livewire\BaseSummoner::class)->name('champions');
Route::get('/summoner/{summonerId}/encounters', \App\Http\Livewire\BaseSummoner::class)->name('encounters');
Route::get('/summoner/{summonerId}/live-game', \App\Http\Livewire\BaseSummoner::class)->name('live_game');
Route::get('/sync', [\App\Http\Controllers\SyncLolController::class, 'index'])->name('sync');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
