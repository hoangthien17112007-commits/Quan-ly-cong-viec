<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Book;
use App\Models\Location;

class DashboardHome extends Component
{
    public $stats = [];

    public function render()
    {
        if (auth()->user()->can("user.view")) {
            $this->stats['users'] = User::count();
        }
        if (auth()->user()->can("role.view")) {
            $this->stats['roles'] = \Spatie\Permission\Models\Role::count();
        }
        if (auth()->user()->can("book.view")) {
            $this->stats['books'] = Book::count();
        }
        if (auth()->user()->can("location.view")) {
            $this->stats['locations'] = Location::count();
        }

        return view('livewire.dashboard-home');
    }
}
