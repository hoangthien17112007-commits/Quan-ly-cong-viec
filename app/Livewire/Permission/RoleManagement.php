<?php

namespace App\Livewire\Permission;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RoleManagement extends Component
{
    use WithPagination;

    public function create()
    {
        $this->dispatch('createRole');
    }

    public function edit($id)
    {
        $this->dispatch('editRole', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deleteRole', $id);
    }
    #[On('refreshPage')]
    public function refreshPage()
    {
        $this->resetPage(); // 🔥 để pagination reload
    }
    public function render()
    {
        return view('livewire.permission.role-management', [
            'roles' => Role::with('permissions')->paginate(10),
        ]);
    }
}
