<div>
    <div class="p-6 min-h-screen">
        <livewire:permission.actions-user-management />

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 p-6 rounded">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                User Management
            </h2>

            @can('user.create')
                <flux:button variant="primary" color="pink" wire:click="create" icon="plus">Add User</flux:button>
            @endcan
        </div>

        {{-- TABLE --}}
        <div class="bg-gray-100 dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                {{-- HEADER --}}
                <thead class="bg-gray-200 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                            Name
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                            Email
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                            Roles
                        </th>
                        @canany(['user.update', 'user.delete'])
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                Actions
                            </th>
                        @endcanany
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}"
                            class="hover:bg-gray-200 dark:hover:bg-gray-700 transition">

                            {{-- NAME --}}
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">
                                {{ $user->name }}
                            </td>

                            {{-- EMAIL --}}
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $user->email }}
                            </td>

                            {{-- ROLES --}}
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-wrap gap-2">

                                    @forelse ($user->roles as $role)
                                        <span wire:key="role-{{ $role->id }}"
                                            class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-semibold">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 text-xs italic">
                                            No role
                                        </span>
                                    @endforelse

                                </div>
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-6 py-4 text-sm flex gap-2">

                                @can('user.update')
                                    <flux:button size="sm" variant="ghost" icon="pencil"
                                        wire:click='edit({{ $user->id }})' />
                                @endcan

                                @can('user.delete')
                                    <flux:button size="sm" variant="ghost" icon="trash"
                                        wire:click='delete({{ $user->id }})' />
                                @endcan

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                Không có user nào
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4 text-gray-900 dark:text-white">
            {{ $users->links() }}
        </div>

    </div>
</div>
