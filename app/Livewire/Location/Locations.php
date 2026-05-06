<?php

namespace App\Livewire\Location;

use App\Models\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Locations extends Component
{

    use AuthorizesRequests;
    public $search = '';
    use WithPagination;
    public function addLocation()
    {
        // dd($this->all());
        $this->authorize('create', Location::class);
        $this->dispatch("addLocation");
    }

    public function editLocation($id)
    {
        //dd($this->all());
        $location=Location::findOrFail($id);
        $this->authorize('update', $location);
        $this->dispatch("editLocation", $id);

    }
    public function deleteLocation($id)
    {
        //dd($this->all());
        $location=Location::findOrFail($id);
        $this->authorize('delete', $location);

        $this->dispatch("deleteLocation", $id);
    }

    #[On("refreshLocations")]
    public function refreshLocations()
    {
        // This method will be called when the "refreshLocations" event is dispatched
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $locations = Location::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('address', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.location.locations', [
            'locations' => $locations
        ]);
    }
}
