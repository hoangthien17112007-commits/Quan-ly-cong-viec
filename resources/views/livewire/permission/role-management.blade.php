<div>
    <livewire:permission.actions-role-management />
    <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                🎯 Role Management
            </h2>

            @can('role.create')
                 <flux:button variant="primary" color="rose" wire:click="create" icon="plus">Add Role</flux:button>
            @endcan
        </div>

        {{-- MESSAGE --}}
        @if (session()->has('message'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-700 border border-green-400">
                {{ session('message') }}
            </div>
        @endif

        {{-- TABLE --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                {{-- HEADER --}}
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Role
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Permissions
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Users
                        </th>
                            @canany(['role.update', 'role.delete'])
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Actions
                        </th>
                            @endcanany
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @forelse ($roles as $role)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition" wire:key="role-{{ $role->id }}">

                            {{-- ROLE NAME --}}
                            <td class="px-6 py-4 font-semibold text-gray-800 dark:text-white">
                                {{ ucfirst($role->name) }}
                            </td>

                            {{-- PERMISSIONS --}}
                            <td class="px-6 py-4">

                                @if ($role->permissions->count())
                                    <span title="{{ $role->permissions->pluck('name')->join(', ') }}"
                                        class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer">
                                        {{ $role->permissions->count() }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs italic">
                                        No permissions
                                    </span>
                                @endif

                            </td>

                            {{-- USER COUNT --}}
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $role->users->count() ?? 0 }}
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-6 py-4 flex gap-2">

                                @can('role.update')
                                     <flux:button size="sm" variant="ghost" icon="pencil"
                                            wire:click='edit({{ $role->id }})' />
                                @endcan

                                @can('role.delete')
                                      <flux:button size="sm" variant="ghost" icon="trash"
                                            wire:click='delete({{ $role->id }})' />
                                @endcan

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-400 dark:text-gray-500">
                                Không có role nào
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $roles->links() }}
        </div>

    </div>
</div>
