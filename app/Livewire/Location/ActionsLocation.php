<?php

namespace App\Livewire\Location;

use App\Models\Location;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ActionsLocation extends Component
{
    public $locationId;

    #[Validate()]
    public $name;
    public $address;
    public $phone;
    public $isUpdateUserMode = false;

    protected $rules = [
        'name' => 'required|unique:locations,name',
        'address' => 'required|unique:locations,address',
        'phone' => 'required|unique:locations,phone'
    ];

    protected $messages = [
        'name.required' => 'Vui lòng nhập tên cửa hàng',
        'name.unique' => 'Tên cửa hàng đã tồn tại!',
        'address.required' => 'Vui lòng nhập địa chỉ',
        'address.unique' => 'Địa chỉ đã tồn tại!',
        'phone.required' => 'Vui lòng nhập số điện thoại',
        'phone.unique' => 'Số điện thoại đã tồn tại!'
    ];

    #[On("addLocation")]
    public function addLocation()
    {
        //dd($this->all());
        $this->reset([
            'name',
            'address',
            'phone',
        ]);

        $this->resetErrorBag();

        Flux::modal('addLocation')->show();
    }
    public function updated($field)
    {
        $this->resetErrorBag($field);
    }


    public function createLocation()
    {
        $this->validate();

        Location::create([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
        ]);

        Flux::modal('addLocation')->close();

        $this->dispatch("refreshLocations");
        Flux::toast(
            text: 'Location đã được tạo!',
            heading: 'Thành công',
            variant: 'success',
            duration: 3000
        );
        $this->dispatch("play-success");
    }
    #[On("editLocation")]
    public function editLocation($id)
    {
        //dd($this->all());
        $location = Location::findOrFail($id);
        $this->locationId = $location->id;
        $this->name = $location->name;
        $this->address = $location->address;
        $this->phone = $location->phone;

         //$this->isUpdateUserMode = true;
        Flux::modal('editLocation')->show();
    }
    public function updateLocation()
    {
        $this->validate([
            'name' => 'required|unique:locations,name,' . $this->locationId . '|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|min:10|max:11'
        ]);

        $location = Location::findOrFail($this->locationId);

        $location->update([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone
        ]);
        $this->resetErrorBag();

        Flux::modal('editLocation')->close();

        $this->dispatch("refreshLocations");

        Flux::toast(
            text: 'Location đã được cập nhật!',
            heading: 'Thành công',
            variant: 'success',
            duration: 3000
        );
        $this->dispatch('play-sound', 'update');


    }
    #[On("deleteLocation")]
    public function confirmDeleteLocation($id)
    {
        $location = Location::findOrFail($id);

        $this->locationId = $location->id;

        Flux::modal('deleteLocation')->show();
    }
    public function deleteLocation()
    {
        $location = Location::findOrFail($this->locationId);

        $location->delete();

        Flux::modal('deleteLocation')->close();

        $this->dispatch("refreshLocations");
        Flux::toast(
            text: 'Location đã được xóa!',
            heading: 'Thành công',
            variant: 'danger',
            duration: 3000
        );
        $this->dispatch('play-sound', 'delete');
    }
    public function render()
    {
        return view('livewire.location.actions-location');
    }
}
