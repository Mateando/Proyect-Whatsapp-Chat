<?php

use App\Http\Controllers\ContactController;
use App\Livewire\ChatComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->resource('contacts', ContactController::class)->names('contacts')->except('show');

Route::get('/chat', ChatComponent::class)->name('chat.index')->middleware('auth');

