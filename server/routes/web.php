<?php

use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LivewireTestController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('calendar');
});

// Route::middleware(['auth:sanctum', 'verified'])
//     ->get('/dashboard', function () {
//         return view('dashboard');
//     })
//     ->name('dashboard');

Route::prefix('manager')
    ->middleware('can:manager-higher')
    ->group(function () {
        Route::get('events/past', [EventController::class, 'past'])->name('events.past');
        Route::resource('events', EventController::class);
    });

Route::middleware('can:user-higher')->group(function () {
    Route::get('/dashboard', [ReservationController::class, 'dashboard'])->name('dashboard');
    Route::get('/{id}', [ReservationController::class, 'detail'])->name('events.detail');
    Route::post('/{id}', [ReservationController::class, 'reserve'])->name('events.reserve');
});

// localhost/livewire-test/index
Route::controller(LivewireTestController::class)
    ->prefix('livewire-test')
    ->name('livewire-test.')
    ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('register', 'register')->name('register');
    });

Route::get('alpine-test/index', [AlpineTestController::class, 'index']);
