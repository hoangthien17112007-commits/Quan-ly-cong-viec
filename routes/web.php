<?php

use App\Livewire\Book\Books;
use App\Livewire\Location\Locations;
use App\Livewire\Permission\RoleManagement;
use App\Livewire\Permission\UserManagement;
use App\Livewire\Projects\ProjectBoard;
use App\Livewire\Projects\ProjectList;
use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('locations', Locations::class)->name('locations')->middleware('permission:location.view');
    Route::get('/books', Books::class)->name('books')->middleware('permission:book.view');
    Route::get('/users-management', UserManagement::class)->name('users.index')->middleware('permission:user.view');
    Route::get('/role-management', RoleManagement::class)->name('role-management')->middleware('permission:role.view');
});
// Nhóm các route liên quan đến Project
Route::prefix('projects')->name('projects.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', ProjectList::class)->name('index');
    Route::get('/{project:slug}', ProjectBoard::class)->name('board');
});
require __DIR__ . '/settings.php';
