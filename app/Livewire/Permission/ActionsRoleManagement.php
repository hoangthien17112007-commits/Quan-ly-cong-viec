<?php

namespace App\Livewire\Permission;

use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ActionsRoleManagement extends Component
{
    use WithPagination;

    public $roleId;
    public $name;
    public $selectedPermissions = [];
    public $isEditing = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
            'selectedPermissions' => 'array',
        ];
    }

    #[On('createRole')]
    public function createRole()
    {
        $this->authorize('role.create');
        $this->resetForm();
        $this->isEditing = false;
        Flux::modal('createRole')->show();
    }

    #[On('editRole')]
    public function editRole($id)
    {
        $this->authorize('role.update');
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
        Flux::modal('createRole')->show();
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $role = Role::findOrFail($this->roleId);
            $role->update(['name' => $this->name]);
        } else {
            $role = Role::create(['name' => $this->name]);
        }

        $role->syncPermissions($this->selectedPermissions);
        $this->resetPage();

        Flux::modal('createRole')->close();
    }

    #[On('deleteRole')]
    public function deleteRole($id)
    {
        $this->authorize('role.delete');
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        Flux::modal('deleteRole')->show();
    }

    public function confirmDelete()
    {
        $role = Role::findOrFail($this->roleId);
        $role->delete();
        $this->resetPage();
        Flux::modal('deleteRole')->close();
    }

    public function resetForm()
    {
        $this->roleId = null;
        $this->name = '';
        $this->selectedPermissions = [];
        $this->isEditing = false;
    }

    public function render()
    {
        return view('livewire.permission.actions-role-management', [
            'permissions' => Permission::all()->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            }),
        ]);
    }
}
