<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <flux:heading size="xl">Dự án của tôi</flux:heading>
            <flux:subheading>Quản lý và theo dõi tất cả các kế hoạch đang tham gia.</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="$dispatch('addProject')">
            Dự án mới
        </flux:button>
    </div>

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Tìm kiếm dự án..." icon="magnifying-glass"
            class="flex-1" />

        <select wire:model.live="status"
            class="sm:w-48 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">Tất cả trạng thái</option>
            @foreach(\App\Condition\ProjectStatus::cases() as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
        </select>
    </div>

    {{-- Danh sách dự án --}}
    @if($projects->isEmpty())
        <div
            class="flex flex-col items-center justify-center py-20 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <flux:icon name="folder-plus" variant="outline" class="size-12 text-zinc-300 mb-4" />
            <flux:heading>Chưa có dự án nào</flux:heading>
            <flux:subheading>Hãy bắt đầu bằng cách tạo dự án đầu tiên của bạn.</flux:subheading>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <flux:card class="flex flex-col h-full hover:shadow-lg transition-all group relative">
                    <div class="flex justify-between items-start mb-4">
                        @php $projectStatus = \App\Condition\ProjectStatus::tryFrom($project->status) @endphp
                        <flux:badge size="sm" :color="$projectStatus?->color() ?? 'zinc'" variant="outline">
                            {{ $projectStatus?->label() ?? strtoupper($project->status) }}
                        </flux:badge>

                        {{-- Nút sửa / xoá --}}
                        <div class="flex gap-1">
                            <flux:button variant="ghost" size="xs" icon="pencil-square"
                                wire:click.stop="$dispatch('editProject', { id: {{ $project->id }} })"
                                class="opacity-0 group-hover:opacity-100 transition-opacity" />
                            <flux:button variant="ghost" size="xs" icon="trash"
                                wire:click.stop="$dispatch('deleteProject', { id: {{ $project->id }} })"
                                class="opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>
                    </div>

                    <a href="{{ route('projects.board', $project->slug) }}" class="flex-1 block">
                        <flux:heading size="lg" class="group-hover:text-primary-600 transition-colors">
                            {{ $project->name }}
                        </flux:heading>

                        <flux:text class="line-clamp-2 mt-2 text-sm">
                            {{ filled($project->description) ? $project->description : 'Không có mô tả dự án.' }}
                        </flux:text>
                    </a>

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                        {{-- Người tạo --}}
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                @if($project->creator)
                                    <flux:avatar size="xs" :initials="$project->creator->initials()" />
                                    <flux:text size="xs">{{ $project->creator->name }}</flux:text>
                                @else
                                    <flux:text size="xs" class="text-zinc-400">Không có người tạo</flux:text>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 text-zinc-400">
                                <flux:icon name="clipboard-document-list" variant="micro" />
                                <flux:text size="xs">{{ $project->tasks_count }} tasks</flux:text>
                            </div>
                        </div>

                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    {{-- Component xử lý Sửa/Xóa dự án --}}
    <livewire:projects.project-settings />
</div>