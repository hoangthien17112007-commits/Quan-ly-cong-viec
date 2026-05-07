<div class="flex flex-col h-screen">

    {{-- Header --}}
    <div
        class="flex justify-between items-center px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex-shrink-0">
        <div>
            <flux:heading size="xl">{{ $project->name }}</flux:heading>
            @if($project->description)
                <flux:subheading>{{ $project->description }}</flux:subheading>
            @endif
        </div>

        <div class="flex items-center gap-3">
            @if($project->Leader)
                <flux:tooltip content="Leader: {{ $project->Leader->name }}">
                    <flux:avatar size="sm" :initials="$project->Leader->initials()"
                        class="ring-2 ring-white dark:ring-zinc-900" />
                </flux:tooltip>
            @endif

            <flux:button icon="cog-6-tooth" variant="ghost" size="sm"
                wire:click="$dispatch('editProject', { id: {{ $project->id }} })" />
            <flux:button icon="archive-box" variant="ghost" size="sm" wire:click="$dispatch('open-archive')">
            </flux:button>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="flex-1 overflow-x-auto overflow-y-hidden p-6 min-h-0">
        <flux:kanban wire:sort="updateGroupOrder"
            wire:sort:config="{ animation: 180, ghostClass: 'kanban-column-ghost', chosenClass: 'kanban-column-chosen', dragClass: 'kanban-column-drag' }"
            class="h-full items-start">

            @foreach($project->taskGroups as $group)
                <flux:kanban.column wire:key="group-{{ $group->id }}" wire:sort:item="{{ $group->id }}">

                    {{-- Header cột: inline rename --}}
                    <flux:kanban.column.header :count="$group->tasks->count()" class="kanban-column-header">
                        <x-slot name="heading">
                            <button type="button" wire:sort:handle title="Kéo để sắp xếp danh sách"
                                class="kanban-column-drag-handle -ml-1 mr-1 flex size-6 shrink-0 items-center justify-center rounded-md text-zinc-400 hover:bg-zinc-200 hover:text-zinc-700 dark:hover:bg-zinc-700 dark:hover:text-zinc-200">
                                <flux:icon name="bars-3" variant="micro" />
                            </button>
                            <div x-data="{
                                                                                                                                    editing: false,
                                                                                                                                    title: '{{ addslashes($group->title) }}',
                                                                                                                                    save() {
                                                                                                                                        if (this.title.trim() === '') return;
                                                                                                                                        $wire.dispatchTo(
                                                                                                                                            'projects.actions-task-group',
                                                                                                                                            'save-rename',
                                                                                                                                            { groupId: {{ $group->id }}, newTitle: this.title }
                                                                                                                                        );
                                                                                                                                        this.editing = false;
                                                                                                                                    }
                                                                                                                                }"
                                class="flex-1">
                                <span x-show="!editing"
                                    x-on:click="editing = true; $nextTick(() => $refs.input{{ $group->id }}.focus())"
                                    class="cursor-pointer hover:text-blue-500 transition-colors font-semibold text-sm block">
                                    {{ $group->title }}
                                </span>
                                <input x-ref="input{{ $group->id }}" x-show="editing" x-model="title"
                                    x-on:keydown.enter="save()"
                                    x-on:keydown.escape="editing = false; title = '{{ addslashes($group->title) }}'"
                                    x-on:blur="save()"
                                    class="w-full bg-transparent font-semibold text-sm outline-none border-b border-zinc-400 dark:border-zinc-500 focus:border-blue-500" />
                            </div>
                        </x-slot>

                        <x-slot name="actions">
                            <flux:dropdown>
                                <flux:button variant="subtle" icon="ellipsis-horizontal" size="sm" />
                                <flux:menu>
                                    <flux:menu.item icon="plus" x-on:click="$dispatch('open-inline-add-{{ $group->id }}')">
                                        Thêm thẻ
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="arrow-right-circle"
                                        wire:click="$dispatch('start-move-group', { groupId: {{ $group->id }} })">
                                        Di chuyển nhóm
                                    </flux:menu.item>
                                    <flux:menu.item icon="archive-box"
                                        wire:click="$dispatch('archive-group', { groupId: {{ $group->id }} })"
                                        wire:confirm="Lưu trữ nhóm này? Bạn có thể khôi phục sau.">
                                        Lưu trữ nhóm
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item variant="danger" icon="trash"
                                        wire:click="$dispatch('delete-group', { groupId: {{ $group->id }} })"
                                        wire:confirm="Xóa nhóm này sẽ xóa toàn bộ task bên trong. Bạn chắc chắn?">
                                        Xóa nhóm
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </x-slot>
                    </flux:kanban.column.header>

                    {{-- Danh sách task --}}
                    <flux:kanban.column.cards wire:sort="updateTaskOrder" wire:sort:group="tasks"
                        wire:sort:group-id="{{ $group->id }}"
                        wire:sort:config="{ animation: 150, ghostClass: 'kanban-card-ghost', chosenClass: 'kanban-card-chosen', dragClass: 'kanban-card-drag' }">
                        @foreach($group->tasks as $task)
                                    <flux:kanban.card as="div" role="button" tabindex="0" wire:key="task-{{ $task->id }}"
                                        wire:sort:item="{{ $task->id }}" wire:click="$dispatch('editTask', { id: {{ $task->id }} })">

                                        <x-slot name="header">
                                            <flux:badge size="sm"
                                                :color="match($task->priority) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        'urgent' => 'red',
                                                                                                                                                                                                                                                                                                                                                                                                                                                        'high'   => 'orange',
                                                                                                                                                                                                                                                                                                                                                                                                                                                        'medium' => 'yellow',
                                                                                                                                                                                                                                                                                                                                                                                                                                                        default  => 'zinc'
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }">
                                                {{ ucfirst($task->priority) }}
                                            </flux:badge>
                                        </x-slot>

                                        <div class="flex items-start gap-2">
                                            <button wire:click.stop="toggleTask({{ $task->id }})" class="mt-0.5 flex-shrink-0 size-4 rounded-full border-2 transition-all
                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $task->status === \App\Condition\TaskStatus::DONE
                            ? 'bg-green-500 border-green-500'
                            : 'border-zinc-300 hover:border-green-400' }}">
                                            </button>
                                            <span class="text-sm font-medium text-left
                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $task->status === \App\Condition\TaskStatus::DONE
                            ? 'line-through text-zinc-400' : '' }}">
                                                {{ $task->name }}
                                            </span>
                                        </div>

                                        <x-slot name="footer">
                                            @if($task->deadline_at)
                                                <span class="flex items-center gap-1 text-[10px] text-zinc-400">
                                                    <flux:icon name="calendar" variant="micro" />
                                                    {{ $task->deadline_at->format('d/m') }}
                                                </span>
                                            @endif
                                            @if($task->assignee)
                                                <flux:tooltip content="{{ $task->assignee->name }}">
                                                    <flux:avatar circle size="xs" :initials="$task->assignee->initials()" />
                                                </flux:tooltip>
                                            @endif
                                        </x-slot>
                                    </flux:kanban.card>
                        @endforeach

                        {{-- Inline add task --}}
                        <div x-data="{
                                                                                                                                    open: false,
                                                                                                                                    name: '',
                                                                                                                                    submit() {
                                                                                                                                        if (this.name.trim() === '') return;
                                                                                                                                        $wire.dispatchTo('tasks.actions-task', 'quick-add-task', {
                                                                                                                                            groupId: {{ $group->id }},
                                                                                                                                            name: this.name.trim()
                                                                                                                                        });
                                                                                                                                        this.name = '';
                                                                                                                                        this.open = false;
                                                                                                                                    }
                                                                                                                                }"
                            x-on:open-inline-add-{{ $group->id }}.window="open = true; $nextTick(() => $refs.quickInput{{ $group->id }}.focus())">
                            <div x-show="open" x-cloak class="space-y-2 p-1">
                                <textarea x-ref="quickInput{{ $group->id }}" x-model="name"
                                    x-on:keydown.enter.prevent="submit()" x-on:keydown.escape="open = false; name = ''"
                                    placeholder="Nhập tên thẻ..." rows="2"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600
                                                                                                                                               bg-white dark:bg-zinc-800 text-sm px-3 py-2
                                                                                                                                               focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                <div class="flex items-center gap-2">
                                    <flux:button size="sm" variant="primary" x-on:click="submit()">
                                        Thêm thẻ
                                    </flux:button>
                                    <flux:button size="sm" variant="ghost" x-on:click="open = false; name = ''">
                                        <flux:icon name="x-mark" variant="micro" />
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </flux:kanban.column.cards>

                    {{-- Footer cột --}}
                    <flux:kanban.column.footer>
                        <flux:button variant="subtle" icon="plus" size="sm" align="start"
                            x-on:click="$dispatch('open-inline-add-{{ $group->id }}')">
                            Thêm thẻ
                        </flux:button>
                    </flux:kanban.column.footer>

                </flux:kanban.column>
            @endforeach

            {{-- Cột thêm nhóm mới --}}
            <flux:kanban.column>
                <flux:kanban.column.cards>
                    <livewire:projects.actions-task-group :project-id="$project->id" :key="'atg-' . $project->id" />
                </flux:kanban.column.cards>

                <flux:kanban.column.footer>
                    <flux:button variant="subtle" icon="plus" size="sm" align="start"
                        wire:click="$dispatchTo('projects.actions-task-group', 'show-new-group-form')">
                        Thêm danh sách
                    </flux:button>
                </flux:kanban.column.footer>
            </flux:kanban.column>

        </flux:kanban>

    </div>


    <style>
        [data-flux-kanban] {
            height: 100%;
            align-items: flex-start;
        }

        /* Dùng max-height thay vì height cố định
       → cột ngắn theo nội dung, cột nhiều task mới scroll */
        [data-flux-kanban-column]>div {
            max-height: calc(100vh - 121px) !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .kanban-column-header {
            user-select: none;
        }

        .kanban-column-drag-handle {
            cursor: grab;
            touch-action: none;
        }

        .kanban-column-drag-handle:active,
        .kanban-column-chosen .kanban-column-drag-handle {
            cursor: grabbing;
        }

        .kanban-column-chosen>div,
        .kanban-column-drag>div {
            transform: rotate(1deg);
            box-shadow: 0 18px 38px rgb(0 0 0 / 18%);
        }

        .kanban-column-ghost>div {
            opacity: .45;
            outline: 2px dashed #94a3b8;
            outline-offset: -2px;
        }

        .kanban-card-chosen,
        .kanban-card-drag {
            cursor: grabbing;
            transform: rotate(1deg);
            box-shadow: 0 12px 24px rgb(0 0 0 / 16%);
        }

        .kanban-card-ghost {
            opacity: .45;
            outline: 2px dashed #94a3b8;
            outline-offset: -2px;
        }

        [data-flux-kanban-column-cards] {
            flex: 1 !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
        }

        [data-flux-kanban-column-cards]:hover {
            scrollbar-color: #d1d5db transparent;
        }

        [data-flux-kanban-column-cards]::-webkit-scrollbar {
            width: 4px;
        }

        [data-flux-kanban-column-cards]::-webkit-scrollbar-track {
            background: transparent;
        }

        [data-flux-kanban-column-cards]::-webkit-scrollbar-thumb {
            background-color: transparent;
            border-radius: 99px;
        }

        [data-flux-kanban-column-cards]:hover::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
        }

        [data-flux-kanban-column-cards]:hover::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
        }

        .dark [data-flux-kanban-column-cards]:hover {
            scrollbar-color: #52525b transparent;
        }

        .dark [data-flux-kanban-column-cards]:hover::-webkit-scrollbar-thumb {
            background-color: #52525b;
        }
    </style>

    {{-- Modals --}}
    <livewire:projects.project-settings />
    <livewire:tasks.task-archive :project="$project" />
    <livewire:tasks.actions-task :project="$project" />

</div>