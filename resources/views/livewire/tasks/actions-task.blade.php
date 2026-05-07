<flux:modal name="task-detail-modal" class="w-full max-w-[95vw] h-[90vh]">
    <div class="flex flex-col h-full">
        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center border-b px-6 py-4">
            <div class="flex items-center gap-3 w-full">
                {{-- checkbox --}}
                <input type="checkbox" wire:click="toggleTaskDone" @checked($taskIsDone) class="w-5 h-5 cursor-pointer">

                {{-- ✅ TITLE --}}
                <input type="text" wire:model.blur="taskTitle"
                    class="text-xl font-semibold w-full bg-transparent border-none focus:outline-none"
                    placeholder="Nhập tên task..." />
            </div>

            <flux:dropdown>
                <flux:button icon="ellipsis-horizontal" variant="ghost" />
                <flux:menu>
                    <flux:menu.item>Tham gia</flux:menu.item>
                    <flux:menu.item>Di chuyển</flux:menu.item>
                    <flux:menu.item>Sao chép</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item variant="danger" wire:click="deleteTask"
                        wire:confirm="Bạn có chắc chắn muốn xóa task này?">Lưu trữ</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>

        {{-- ================= BODY ================= --}}
        <div class="flex flex-1 overflow-hidden">
            {{-- ===== LEFT ===== --}}
            <div class="w-2/3 p-6 space-y-6 overflow-y-auto">
                <div class="flex gap-2 flex-wrap">
                    <flux:button size="sm">Thêm</flux:button>
                    <flux:button size="sm">Nhãn</flux:button>
                    <flux:button size="sm">Ngày</flux:button>
                    <flux:button size="sm">Checklist</flux:button>
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <h3 class="font-semibold mb-2">Mô tả</h3>
                    <flux:editor wire:model.blur="taskDescription">
                        <flux:editor.toolbar>
                            <flux:editor.bold />
                            <flux:editor.italic />
                            <flux:editor.strike />
                            <flux:editor.separator />
                            <flux:editor.bullet />
                            <flux:editor.ordered />
                            <flux:editor.blockquote />
                            <flux:editor.separator />
                            <flux:editor.link />
                        </flux:editor.toolbar>
                        <flux:editor.content />
                    </flux:editor>
                </div>
            </div>

            {{-- ===== RIGHT ===== --}}
            <div class="w-1/3 border-l p-6 space-y-4 overflow-y-auto bg-zinc-50/30 dark:bg-zinc-900/30">
                <h3 class="font-semibold">Hoạt động</h3>
                {{-- COMMENT --}}
                <flux:editor wire:model="newComment" placeholder="Viết bình luận...">
                    <flux:editor.toolbar>
                        <flux:editor.bold />
                        <flux:editor.italic />
                        <flux:editor.link />
                    </flux:editor.toolbar>
                    <flux:editor.content />
                </flux:editor>

                <flux:button wire:click="addTaskComment" size="sm">Gửi</flux:button>

                {{-- ACTIVITY --}}
                <div class="mt-4 space-y-3">
                    @if($currentTask)
                        @foreach($currentTask->activities as $activity)
                            <div class="text-sm p-2 rounded border border-zinc-200 dark:border-zinc-700">
                                <strong class="text-zinc-800 dark:text-zinc-200">{{ $activity->user?->name }}</strong>
                                <p class="text-zinc-600 dark:text-zinc-400">{{ $activity->description }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</flux:modal>