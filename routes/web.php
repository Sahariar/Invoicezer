<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/info', function () {
    return phpinfo();
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::view('invoice', 'livewire.invoices.invoice-list')->name('invoices');
    Route::view('invoice/view', 'livewire.invoices.view-invoice')->name('invoices.view');
    Route::view('invoice/create', 'livewire.invoices.create-invoice')->name('invoices.create');
    Route::view('invoice/edit', 'livewire.invoices.edit-invoice')->name('invoices.edit');

});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});




require __DIR__.'/auth.php';
