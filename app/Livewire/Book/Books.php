<?php

namespace App\Livewire\Book;

use App\Models\Book;
use App\Models\Location;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Books extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $locationFilter = '';
    public $sortBy = 'published_year';
    public $sortDirection = 'desc';


    protected $casts = [
        'type' => 'array'
    ];
    public function addBook()
    {
        $this->dispatch('addBook');
    }
    public function editBook($id)
    {
        //dd($id);
        $this->dispatch('editBook', $id);
    }
    public function deleteBook($id)
    {
        $this->dispatch('deleteBook', $id);
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
    public function getLocationsProperty()
    {
        return Location::query()->get(['id', 'name']);
    }

    #[On('bookUpdated')]
    public function refreshList()
    {
        // reset về page 1 nếu cần
        $this->resetPage();
    }
    public function getBooksProperty()
    {
        return Book::query()

            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })

            ->when($this->typeFilter, function ($query) {
                $query->whereJsonContains('type', $this->typeFilter);
            })
            ->when($this->locationFilter, function ($query) {
                $query->where('location_id', (int) $this->locationFilter);
            })

            ->orderBy($this->sortBy, $this->sortDirection)

            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.book.books', [
            'books' => $this->books
        ]);
    }
}
