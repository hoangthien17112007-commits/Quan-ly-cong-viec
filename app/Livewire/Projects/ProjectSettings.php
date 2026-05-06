<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ProjectSettings extends Component
{
    public $name, $description, $date_deadline, $time_deadline;
    public $isUpdateProject = false;
    public $projectId;
    public $status = 'pending';
    public $wipLimit = null;

    public $assignedLeader = null;

    public $selectedMembers = [];
    public $newMemberId = null;

    #[On('addProject')]
    public function addProject()
    {
        $this->resetData();
        $this->isUpdateProject = false;
        Flux::modal('action-project')->show();
    }

    public function createProject()
    {
        $this->validate();

        $project = Project::create([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
            'description' => $this->description,
            'created_by' => Auth::id(),
            'assigned_leader' => $this->assignedLeader ?: null,
            'deadline_at' => $this->buildDeadline(),
            'status' => 'pending',
            'wip_limit' => $this->wipLimit ?: null,
        ]);

        $members[Auth::id()] = ['role' => Auth::user()->getRoleNames()->first() ?? 'member'];
        $project->users()->attach($members);

        Flux::modal('action-project')->close();
        Flux::toast(heading: 'Thành công', text: 'Thêm dự án thành công.', variant: 'success');

        $this->dispatch('reloadData');
    }

    #[On('editProject')]
    public function editProject($id)
    {
        $this->resetData();
        $project = Project::with('users')->findOrFail($id);

        $this->projectId = $project->id;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->status = $project->status;
        $this->assignedLeader = $project->assigned_leader ?? null;
        $this->wipLimit = $project->wip_limit;

        $this->selectedMembers = $project->users->pluck('id')->toArray();

        if ($project->deadline_at) {
            $deadline = Carbon::parse($project->deadline_at);
            $this->date_deadline = $deadline->toDateString();
            $this->time_deadline = $deadline->format('H:i');
        }

        $this->isUpdateProject = true;
        Flux::modal('action-project')->show();
    }

    public function updateProject()
    {
        $this->validate();

        $project = Project::findOrFail($this->projectId);

        $project->update([
            'name' => $this->name,
            'description' => $this->description,
            'deadline_at' => $this->buildDeadline(),
            'status' => $this->status,
            'assigned_leader' => $this->assignedLeader ?: null,
            'wip_limit' => $this->wipLimit ?: null,
        ]);

        $members = collect($this->selectedMembers)
            ->mapWithKeys(fn($id) => [$id => ['role' => User::find($id)?->getRoleNames()->first() ?? 'member']])
            ->toArray();
        $members[$project->created_by] = ['role' => Auth::user()->getRoleNames()->first() ?? 'member'];
        $project->users()->sync($members);

        Flux::modal('action-project')->close();
        Flux::toast(heading: 'Thành công', text: 'Cập nhật dự án thành công.', variant: 'success');

        $this->dispatch('reloadData');
    }

    #[On('deleteProject')]
    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $this->projectId = $project->id;
        $this->name = $project->name;

        Flux::modal('delete-project')->show();
    }

    public function deleteProjectConfirm()
    {
        $project = Project::findOrFail($this->projectId);
        $project->delete();

        Flux::modal('delete-project')->close();
        Flux::toast(heading: 'Thành công', text: 'Xóa dự án thành công.', variant: 'success');

        $this->dispatch('reloadData');
    }

    public function addMember()
    {
        if ($this->newMemberId && !in_array($this->newMemberId, $this->selectedMembers)) {
            $this->selectedMembers[] = $this->newMemberId;
        }
        $this->newMemberId = null;
    }

    public function removeMember($memberId)
    {
        $this->selectedMembers = array_values(
            array_filter($this->selectedMembers, fn($id) => $id != $memberId)
        );
    }

    public function resetData()
    {
        $this->reset([
            'name',
            'description',
            'date_deadline',
            'time_deadline',
            'assignedLeader',
            'selectedMembers',
            'newMemberId',
            'projectId',
            'status',
            'wipLimit',
        ]);
        $this->status = 'pending';
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:projects,name,' . ($this->projectId ?? 'NULL'),
            'description' => 'nullable|string',
            'assignedLeader' => 'nullable|exists:users,id',
            'selectedMembers' => 'nullable|array',
            'selectedMembers.*' => 'exists:users,id',
            'date_deadline' => 'nullable|date',
            'time_deadline' => 'nullable',
            'wipLimit' => 'nullable|integer|min:1',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Tên dự án là bắt buộc.',
            'name.max' => 'Tên dự án không quá 255 ký tự.',
            'date_deadline.date' => 'Định dạng ngày không hợp lệ.',
            'wipLimit.integer' => 'Giới hạn WIP phải là số nguyên.',
            'wipLimit.min' => 'Giới hạn WIP phải ít nhất là 1.',
        ];
    }

    private function buildDeadline(): ?Carbon
    {
        if (!$this->date_deadline || !$this->time_deadline)
            return null;

        return Carbon::createFromFormat(
            'Y-m-d H:i',
            $this->date_deadline . ' ' . $this->time_deadline,
            config('app.timezone')
        );
    }

    public function render()
    {
        return view('livewire.projects.project-settings', [
            'users' => User::orderBy('name')->get(),
            'projectName' => $this->name,
        ]);
    }
}