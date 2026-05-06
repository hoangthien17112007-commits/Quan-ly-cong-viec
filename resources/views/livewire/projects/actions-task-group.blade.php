{{-- ActionsTaskGroup — Headless component: form + modals --}}
<div>

    {{-- Form tạo group mới --}}
    @if($showNewGroupForm)
        <div class="space-y-2 p-1">
            <flux:input wire:model="newGroupTitle" placeholder="Tên nhóm..." wire:keydown.enter="addGroup"
                wire:keydown.escape="$set('showNewGroupForm', false)" autofocus />
            <div class="flex gap-2">
                <flux:button variant="primary" size="sm" wire:click="addGroup">Thêm</flux:button>
                <flux:button variant="ghost" size="sm" wire:click="$set('showNewGroupForm', false)">Hủy</flux:button>
            </div>
        </div>
    @endif

    {{-- Modal: Di chuyển group sang project khác --}}
    <flux:modal name="move-group" class="max-w-md">
        {{-- ✅ Bỏ <form> — dùng div thay thế --}}
            <div class="space-y-4">
                <flux:heading size="lg">Di chuyển nhóm</flux:heading>
                <flux:subheading>Chọn dự án đích để chuyển nhóm này (kèm toàn bộ task bên trong).</flux:subheading>

                @if(count($availableProjects) === 0)
                    <div class="py-4 text-center text-sm text-zinc-500 dark:text-zinc-400">
                        Không có dự án nào khác để chuyển.
                    </div>
                @else
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($availableProjects as $proj)
                                    <label wire:key="move-proj-{{ $proj['id'] }}"
                                        class="flex items-center justify-between p-3 rounded-lg border transition-all
                                                                {{ $targetProjectId == $proj['id']
                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                            : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300' }}
                                                                {{ !$proj['can_accept'] ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">

                                        <div class="flex items-center gap-3">
                                            {{-- ✅ Thêm .live để cập nhật ngay khi chọn --}}
                                            <input type="radio" wire:model.live="targetProjectId" value="{{ $proj['id'] }}"
                                                class="accent-blue-500" {{ !$proj['can_accept'] ? 'disabled' : '' }} />
                                            <span class="text-sm font-medium">{{ $proj['name'] }}</span>
                                        </div>

                                        @if($proj['wip_limit'] !== null)
                                            <flux:badge size="sm" :color="$proj['can_accept'] ? 'green' : 'red'">
                                                {{ $proj['wip_current'] }}/{{ $proj['wip_limit'] }}
                                                @if(!$proj['can_accept']) — Đã đầy @endif
                                            </flux:badge>
                                        @else
                                            <flux:badge size="sm" color="zinc">Không giới hạn</flux:badge>
                                        @endif
                                    </label>
                        @endforeach
                    </div>
                @endif

                {{-- Chọn vị trí (chỉ hiện sau khi chọn project) --}}
                @if($targetProjectId && count($targetPositions) > 0)
                    <flux:select wire:model.live="targetPosition" label="Vị trí">
                        @foreach($targetPositions as $pos)
                            <option value="{{ $pos }}">
                                {{ $pos }}
                                @if($pos === count($targetPositions)) (cuối) @endif
                            </option>
                        @endforeach
                    </flux:select>
                @endif

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="ghost" wire:click="closeMoveModal">Hủy</flux:button>
                    <flux:button variant="primary" wire:click="moveGroup" :disabled="$targetProjectId === null">
                        Chuyển
                    </flux:button>
                </div>
            </div>
    </flux:modal>

</div>