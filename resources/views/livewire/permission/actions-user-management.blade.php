<div>
    <flux:modal name="userModal" class="md:w-[500px]">

        <form class="space-y-6">

            {{-- TITLE --}}
            <div>
                <flux:heading size="lg">
                    {{ $isEditing ? 'Edit User' : 'Add User' }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ $isEditing ? 'Cập nhật thông tin user.' : 'Tạo user mới.' }}
                </flux:text>
            </div>

            {{-- NAME --}}
            <flux:input wire:model.defer="name" label="Name" placeholder="Enter name" />

            {{-- EMAIL --}}
            <flux:input wire:model.defer="email" label="Email" type="email" placeholder="Enter email" />


            {{-- PASSWORD --}}
            <flux:input wire:model.defer="password" label="Password" type="password"
                placeholder="{{ $isEditing ? 'Leave blank to keep old password' : 'Enter password' }}" />

            {{-- ROLES --}}
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Roles
                </label>



                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($roles as $role)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" value="{{ $role->name }}" wire:model="selectedRoles"
                                class="rounded">
                            <span class="text-sm">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex">

                <flux:spacer />

                {{-- CLOSE --}}
                <flux:modal.close>
                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                {{-- SAVE --}}
                <flux:button wire:click="save" variant="primary" class="ml-2">
                    {{ $isEditing ? 'Update' : 'Create' }}
                </flux:button>

            </div>
        </form>
    </flux:modal>


    <flux:modal name="deleteModal" class="md:w-96">

        <form class="space-y-6 text-center">

            <flux:heading size="lg">
                Xác nhận xóa
            </flux:heading>

            <flux:text>
                Bạn có chắc muốn xóa user này không?
            </flux:text>

            <div class="flex justify-center gap-3">

                <flux:modal.close>
                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="confirmDelete">
                    Xóa
                </flux:button>

            </div>

        </form>

    </flux:modal>
</div>
