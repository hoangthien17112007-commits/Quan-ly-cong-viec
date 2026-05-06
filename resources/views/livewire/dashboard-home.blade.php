<div class="p-6 min-h-screen">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @if (auth()->user()->can("user.view"))
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Total Users</h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['users'] ?? 0 }}</p>
            </div>
        @endif
        @if (auth()->user()->can("role.view"))
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Total Roles</h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['roles'] ?? 0 }}</p>
            </div>
        @endif
        @if (auth()->user()->can("book.view"))
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Total Books</h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['books'] ?? 0 }}</p>
            </div>
        @endif
        @if (auth()->user()->can("location.view"))
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Total Locations</h3>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['locations'] ?? 0 }}</p>
            </div>
        @endif
    </div>
    <!-- Quick Actions -->
    <div class="bg-gray-100 dark:bg-neutral-900 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                Quick Actions
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                @can(abilities: 'user.view')
                    <a href="{{ route('users.index') }}"
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                        Manage Users
                    </a>
                @endcan

                @can(abilities: 'book.view')
                    <a href="{{ route('books') }}"
                        class="bg-green-500 hover:bg-green-600 dark:bg-green-700 dark:hover:bg-green-800 text-white px-4 py-2 rounded">
                        Manage Books
                    </a>
                @endcan

                @can(abilities: 'location.view')
                    <a href="{{ route('locations') }}"
                        class="bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-700 dark:hover:bg-yellow-800 text-white px-4 py-2 rounded">
                        Manage Locations
                    </a>
                @endcan

            </div>
        </div>
    </div>
    <!-- User Info -->
    <div class="mt-8 bg-gray-100 dark:bg-neutral-900 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">

            <!-- Title -->
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                Your Account Information
            </h3>

            <!-- Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Name -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Name
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ auth()->user()->name }}
                    </dd>
                </div>

                <!-- Email -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Email
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ auth()->user()->email }}
                    </dd>
                </div>

                <!-- Roles -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Roles
                    </dt>
                    <dd class="mt-1">
                        @foreach(auth()->user()->roles as $role)
                            <span
                                class="inline-flex px-2 py-1 text-xs bg-blue-100 dark:bg-blue-700 text-blue-800 dark:text-white rounded">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </dd>
                </div>

                <!-- Permissions -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Permissions
                    </dt>

                    <dd class="mt-1">
                        <div class="flex flex-wrap gap-1">

                            @foreach(auth()->user()->getAllPermissions()->take(10) as $permission)
                                <span
                                    class="inline-flex px-2 py-1 text-xs bg-green-100 dark:bg-green-700 text-green-800 dark:text-white rounded">
                                    {{ $permission->name }}
                                </span>
                            @endforeach

                            @if(auth()->user()->getAllPermissions()->count() > 10)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    +{{ auth()->user()->getAllPermissions()->count() - 10 }} more
                                </span>
                            @endif

                        </div>
                    </dd>
                </div>

            </div>
        </div>
    </div>
</div>