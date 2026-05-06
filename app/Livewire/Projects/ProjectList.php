<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\On;

class ProjectList extends Component
{
    public string $search = '';
    public string $status = '';

    #[On('reloadData')]
    public function reloadData()
    {
        // Livewire tự re-render khi có sự kiện
    }

    public function render()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $projects = Project::query()
                ->with(['creator', 'Leader'])
                ->withCount('tasks')
                ->when($this->search, fn($q) => $q->where('projects.name', 'like', '%' . $this->search . '%'))
                ->when($this->status, fn($q) => $q->where('projects.status', $this->status))
                ->latest('projects.created_at')
                ->get();
        } else {
            $projects = $user->projects()
                ->with(['creator', 'Leader'])
                ->withCount('tasks')
                ->when($this->search, fn($q) => $q->where('projects.name', 'like', '%' . $this->search . '%'))
                ->when($this->status, fn($q) => $q->where('projects.status', $this->status))
                ->latest('projects.created_at')
                ->get();
        }

        return view('livewire.projects.project-list', [
            'projects' => $projects,
        ]);
    }
}