<div>
    <livewire:location.actions-location />
    <div
        class="bg-gradient-to-br from-blue-50 to-white
               p-8 rounded-3xl
               shadow-xl shadow-blue-200/40
               border border-blue-200
               relative overflow-hidden">

        <!-- Hiệu ứng ánh sáng -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-100 rounded-full blur-3xl opacity-40"></div>

        <!-- Header Row -->
        <div class="flex justify-between items-start relative z-10">

            <!-- Bên trái -->
            <div>
                <flux:heading size="xl" level="1" class="text-3xl font-bold text-blue-800 tracking-tight">
                    {{ __('Quản lý cửa hàng') }}
                </flux:heading>

                <flux:subheading size="lg" class="mt-2 text-blue-600 font-medium">
                    {{ __('Danh sách các cửa hàng của bạn') }}
                </flux:subheading>

                <flux:breadcrumbs class="text-sm font-medium text-blue-700">
                    <flux:breadcrumbs.item href="/dashboard" class="hover:text-blue-700">Home</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item href="/locations" class="hover:text-blue-700">Cửa hàng
                    </flux:breadcrumbs.item>
                    <flux:breadcrumbs.item class="text-blue-800 font-semibold">Danh sách</flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </div>

            @can('location.create')
                <flux:button variant="primary" color="violet" wire:click="addLocation" icon="plus"
                    class="self-center mt-1">
                    Thêm Location
                </flux:button>
            @endcan

        </div>

    </div>

    <flux:separator class="mt-6 border-blue-200" />
    <flux:input wire:model.live.debounce.500ms="search" class="mt-5" placeholder="Tìm kiếm cửa hàng..."
        icon="magnifying-glass" />

    <div class="mt-5">

        <div class="bg-white rounded-2xl shadow-lg border border-blue-100 overflow-hidden">

            <table class="min-w-full text-sm text-left">

                <!-- Header -->
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 font-semibold">#</th>
                        <th class="px-6 py-3 font-semibold">Tên cửa hàng</th>
                        <th class="px-6 py-3 font-semibold">Địa chỉ</th>
                        <th class="px-6 py-3 font-semibold">Số điện thoại</th>
                        @canany(['location.update', 'location.delete'])
                            <th class="px-6 py-3 font-semibold text-center">Hành động</th>
                        @endcanany

                    </tr>
                </thead>

                <!-- Body -->
                <tbody class="divide-y divide-blue-100 bg-white">

                    @foreach ($locations as $location)
                        <tr class="hover:bg-blue-50 transition" wire:key="location-{{ $location->id }}">
                            <td class="px-6 py-4 text-gray-700 font-medium">
                                {{ $locations->firstItem() + $loop->index }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $location->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $location->address }}
                            </td>

                            <td class="px-6 py-4 text-gray-800 font-medium">
                                {{ $location->phone }}
                            </td>

                            @canany(['location.update', 'location.delete'])
                                <td class="px-6 py-4 text-center space-x-2">

                                    @can('location.update')
                                        <flux:button size="sm" variant="ghost" icon="pencil"
                                            wire:click='editLocation({{ $location->id }})' />

                                    @endcan

                                    @can('location.delete')
                                        <flux:button size="sm" variant="ghost" icon="trash"
                                            wire:click='deleteLocation({{ $location->id }})' />
                                    @endcan

                                </td>
                            @endcanany
                        </tr>
                    @endforeach
                    @if ($locations->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Không có cửa hàng nào. Hãy thêm một cửa hàng mới!
                            </td>
                        </tr>
                    @endif

                </tbody>
            </table>

        </div>
        <div class="p-4">
            <flux:pagination :paginator="$locations" />
        </div>

    </div>

</div>
