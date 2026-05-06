<?php

namespace App\Livewire\Permission;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class UserManagement extends Component
{

    public function create()
    {
        $this->dispatch('createUser');
    }

    public function edit($id)
    {
        $this->dispatch('editUser', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deleteUser', $id);
    }
    #[On("refreshPage")]
    public function refreshPage()
    {
       // Refersh bảng user management
    }

    public function render()
    {
        return view('livewire.permission.user-management', [
            'users' => User::with('roles')->paginate(10),

        ]);
    }
}
