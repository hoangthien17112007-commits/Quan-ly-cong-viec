<div>
    {{-- Modal Tạo / Chỉnh sửa dự án --}}
    <flux:modal name="action-project" :title="$isUpdateProject ? 'Chỉnh sửa dự án' : 'Tạo dự án mới'"
        class="md:w-[500px]">
        <form wire:submit="{{ $isUpdateProject ? 'updateProject' : 'createProject' }}" class="space-y-5">

            {{-- Tên dự án --}}
            <flux:input wire:model="name" label="Tên dự án" placeholder="Nhập tên dự án..." />

            {{-- Mô tả --}}
            <flux:textarea wire:model="description" label="Mô tả" placeholder="Dự án này về..." rows="3" />

            {{-- Các trường chỉ hiện khi edit --}}
            @if($isUpdateProject)
                {{-- Thành viên --}}
                <div>
                    <flux:label>Thành viên</flux:label>
                    <div
                        class="mt-1.5 flex flex-wrap gap-2 p-2.5 border border-zinc-200 dark:border-zinc-700 rounded-lg min-h-[42px]">
                        @foreach($selectedMembers as $memberId)
                            @php $member = $users->find($memberId) @endphp
                            @if($member)
                                <flux:badge variant="pill" size="sm">
                                    {{ $member->name }}
                                    <button type="button" wire:click="removeMember({{ $memberId }})"
                                        class="ml-1 opacity-60 hover:opacity-100">&times;</button>
                                </flux:badge>
                            @endif
                        @endforeach

                        <flux:select wire:model="newMemberId" wire:change="addMember"
                            class="border-0 shadow-none text-sm flex-1 min-w-[140px]">
                            <option value="">+ Thêm thành viên</option>
                            @foreach($users->whereNotIn('id', $selectedMembers) as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:error name="selectedMembers" />
                </div>

                {{-- Leader --}}
                <flux:select wire:model="assignedLeader" label="Leader dự án">
                    <option value="">Không có leader</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="assignedLeader" />

                {{-- WIP Limit --}}
                <flux:field>
                    <flux:label>Giới hạn số nhóm (WIP)</flux:label>
                    <flux:description>Để trống nếu không muốn giới hạn số nhóm task trong dự án.</flux:description>
                    <flux:input wire:model="wipLimit" type="number" min="1" placeholder="Không giới hạn..." />
                    <flux:error name="wipLimit" />
                </flux:field>

                {{-- Trạng thái --}}
                <flux:select wire:model="status" label="Trạng thái">
                    @foreach(\App\Condition\ProjectStatus::cases() as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </flux:select>

                {{-- deadline_at: ngày + giờ --}}
                <div class="flex gap-3">
                    <flux:input wire:model="date_deadline" label="Ngày deadline" type="date" class="flex-1" />
                    <flux:input wire:model="time_deadline" label="Giờ" type="time" class="flex-1" />
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex gap-2 justify-end pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Hủy</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
                    {{ $isUpdateProject ? 'Lưu thay đổi' : 'Tạo ngay' }}
                </flux:button>
            </div>

        </form>
    </flux:modal>

    {{-- Modal Xóa dự án --}}
    <flux:modal name="delete-project" title="Xóa dự án" class="md:w-[400px]">
        <form class="space-y-4">
            <flux:text>Bạn có chắc chắn muốn xóa dự án <strong>"{{ $projectName }}"</strong>? Hành động này không thể
                hoàn tác.</flux:text>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Hủy</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="deleteProjectConfirm">Xóa dự án</flux:button>
            </div>
        </form>
    </flux:modal>
</div>