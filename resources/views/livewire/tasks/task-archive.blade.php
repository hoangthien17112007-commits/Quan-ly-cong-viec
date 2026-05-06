<div>
    <flux:modal name="task-archive" class="max-w-lg">
        <div class="space-y-4">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Mục đã lưu trữ</flux:heading>
            </div>

            {{-- Tìm kiếm --}}
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Tìm kiếm..." icon="magnifying-glass" />

            {{-- Tabs --}}
            <div class="flex border-b border-zinc-200 dark:border-zinc-700">
                <button wire:click="$set('activeTab', 'groups')" class="px-4 py-2 text-sm font-medium transition-colors border-b-2
                        {{ $activeTab === 'groups'
    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
    : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Danh sách
                    @if(count($archivedGroups) > 0)
                        <span class="ml-1 text-xs bg-zinc-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded-full">
                            {{ count($archivedGroups) }}
                        </span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'tasks')" class="px-4 py-2 text-sm font-medium transition-colors border-b-2
                        {{ $activeTab === 'tasks'
    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
    : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Thẻ
                    @if(count($archivedTasks) > 0)
                        <span class="ml-1 text-xs bg-zinc-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded-full">
                            {{ count($archivedTasks) }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- Nội dung tab --}}
            <div class="max-h-80 overflow-y-auto space-y-2">

                {{-- Tab: Danh sách (TaskGroup) --}}
                @if($activeTab === 'groups')
                    @forelse($archivedGroups as $group)
                        <div wire:key="ag-{{ $group->id }}"
                            class="flex items-center justify-between p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <div>
                                <flux:text weight="medium" class="text-sm">{{ $group->title }}</flux:text>
                                <flux:text size="xs" class="text-zinc-400 flex items-center gap-1 mt-0.5">
                                    <flux:icon name="archive-box" variant="micro" />
                                    Đã lưu trữ {{ $group->deleted_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:button variant="ghost" size="sm" wire:click="restoreGroup({{ $group->id }})">
                                    Khôi phục
                                </flux:button>
                                <flux:button variant="ghost" size="sm" wire:click="forceDeleteGroup({{ $group->id }})"
                                    wire:confirm="Xóa vĩnh viễn nhóm này? Hành động không thể hoàn tác."
                                    class="text-red-500 hover:text-red-600">
                                    Xóa
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-zinc-400">
                            Không có danh sách nào đã lưu trữ.
                        </div>
                    @endforelse
                @endif

                {{-- Tab: Thẻ (Task) --}}
                @if($activeTab === 'tasks')
                    @forelse($archivedTasks as $task)
                        <div wire:key="at-{{ $task->id }}"
                            class="flex items-center justify-between p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <div>
                                <flux:text weight="medium" class="text-sm">{{ $task->name }}</flux:text>
                                <flux:text size="xs" class="text-zinc-400 flex items-center gap-1 mt-0.5">
                                    <flux:icon name="archive-box" variant="micro" />
                                    {{ $task->group?->title ?? 'Nhóm đã bị xóa' }}
                                    · {{ $task->deleted_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:button variant="ghost" size="sm" wire:click="restoreTask({{ $task->id }})">
                                    Khôi phục
                                </flux:button>
                                <flux:button variant="ghost" size="sm" wire:click="forceDeleteTask({{ $task->id }})"
                                    wire:confirm="Xóa vĩnh viễn task này? Hành động không thể hoàn tác."
                                    class="text-red-500 hover:text-red-600">
                                    Xóa
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-zinc-400">
                            Không có thẻ nào đã lưu trữ.
                        </div>
                    @endforelse
                @endif

            </div>
        </div>
    </flux:modal>
</div>