<div>
    <flux:modal name="createRole" class="md:w-[750px]">

        <form class="space-y-6">

            {{-- TITLE --}}
            <div>
                <flux:heading size="lg">
                    {{ $isEditing ? 'Edit Role' : 'Add Role' }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ $isEditing ? 'Cập nhật role.' : 'Tạo role mới.' }}
                </flux:text>
            </div>

            {{-- ROLE NAME --}}
            <flux:input wire:model.defer="name" label="Role Name" placeholder="Enter role name" />

            {{-- PERMISSIONS --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Permissions
                </label>

                @php
                    $colors = [
                        'user' => 'border-blue-400',
                        'book' => 'border-green-400',
                        'location' => 'border-yellow-400',
                        'role' => 'border-purple-400',
                        'permission' => 'border-red-400',
                    ];
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3">

                    @foreach ($permissions as $group => $perms)
                        <div class="border rounded-lg p-4">

                            <flux:checkbox.group class="space-y-3">
                                {{-- GROUP HEADER --}}
                                <div class="flex items-center justify-between">

                                    <h3 class="font-semibold text-gray-800 dark:text-white">
                                        {{ strtoupper($group) }}
                                    </h3>

                                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                                        <flux:checkbox.all />
                                        <span class="text-gray-500 dark:text-gray-400"></span>
                                    </label>

                                </div>

                                {{-- CHECKBOX LIST --}}
                                <div class="space-y-2">

                                    @foreach ($perms as $perm)
                                        <label class="flex items-center gap-2 text-sm cursor-pointer">

                                            <flux:checkbox value="{{ $perm->name }}"
                                                wire:model.live="selectedPermissions" />

                                            <span class="text-gray-700 dark:text-gray-300">
                                                {{ explode('.', $perm->name)[1] }}
                                            </span>

                                        </label>
                                    @endforeach

                                </div>
                            </flux:checkbox.group>

                        </div>
                    @endforeach

                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex">

                <flux:spacer />

                {{-- CANCEL --}}
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
</div>
