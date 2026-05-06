<?php

namespace App\Livewire\Permission;

use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ActionsUserManagement extends Component
{
    use WithPagination;

    public $userId, $name, $email, $password;
    public $selectedRoles = [];
    public $isEditing = false;
    public $deleteId;
    public $users = [];



    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => $this->isEditing
                ? 'nullable|min:6'
                : 'required|min:6',
            'selectedRoles' => 'array'
        ];
    }

    // ================= CREATE =================
    #[On('createUser')]
    public function createUser()
    {
        $this->resetForm();
        $this->isEditing = false;

        Flux::modal('userModal')->show();
    }

    // ================= EDIT =================
    #[On('editUser')]
    public function editUser($id)
    {
        $this->authorize('user.update');

        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();

        $this->isEditing = true;

        Flux::modal('userModal')->show();
    }

    // ================= SAVE =================
    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->authorize('user.update');
            $user = User::findOrFail($this->userId);

            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
                    ? Hash::make($this->password)
                    : $user->password
            ]);
        } else {
            $this->authorize('user.create');

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password)
            ]);
        }

        $user->syncRoles($this->selectedRoles);

        $this->resetForm();
        Flux::modal('userModal')->close();

        $this->dispatch("refreshPage");
    }

    // ================= DELETE =================
    #[On('deleteUser')]
    public function deleteUser($id)
    {
        $this->deleteId = $id;
        $this->authorize('user.delete');

        User::findOrFail($id);
        Flux::modal('deleteModal')->show();
    }
    public function confirmDelete()
    {
        $this->authorize('user.delete');

        User::findOrFail($this->deleteId)->delete();

        Flux::modal('deleteModal')->close();

        $this->dispatch("refreshPage");
    }

    // ================= RESET =================
    public function resetForm()
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'password',
            'selectedRoles',
            'isEditing'
        ]);

        $this->resetValidation();
    }

    // ================= RENDER =================
    public function render()
    {
        $this->authorize('user.view');

        return view('livewire.permission.actions-user-management', [

            'roles' => Role::all(),
        ]);
    }
}
