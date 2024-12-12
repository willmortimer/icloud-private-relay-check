<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrivateRelayController;

Route::get('/', [PrivateRelayController::class, 'index']);
Route::get('/check-relay', [PrivateRelayController::class, 'check']);
