<div>
    <div>

        <flux:modal name="addLocation" class="md:w-96">

            <form wire:submit.prevent="{{ $isUpdateUserMode ? 'updateLocation' : 'createLocation' }}" class="space-y-6">

                <div>
                    <flux:heading size="lg">
                        {{ $isUpdateUserMode ? 'Cập nhật cơ sở' : 'Thêm mới cơ sở' }}
                    </flux:heading>

                    <flux:text class="mt-2">
                        Quản lý cơ sở trong hệ thống
                    </flux:text>
                </div>

                <flux:input label="Tên cơ sở" placeholder="Nhập tên" wire:model.live.debounce.500ms="name" />


                <flux:input label="Địa chỉ" placeholder="Nhập địa chỉ" wire:model.live.debounce.500ms="address" />

                <flux:input label="Số điện thoại" placeholder="Nhập số điện thoại"
                    wire:model.live.debounce.500ms="phone" />


                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">
                        {{ $isUpdateUserMode ? 'Update' : 'Save' }}
                    </flux:button>

                </div>

            </form>

        </flux:modal>

        <!-- Modal Edit Location -->
        <flux:modal name="editLocation" flyout>

            <form id="updateLocationForm" wire:submit.prevent="updateLocation">

                <div class="space-y-6">

                    <div>
                        <flux:heading size="lg">Update location</flux:heading>
                        <flux:text class="mt-2">
                            Make changes to location.
                        </flux:text>
                    </div>

                    {{-- Name --}}
                    <flux:input label="Name" placeholder="Location name" wire:model.live.debounce.500ms="name" />

                    {{-- Address --}}
                    <flux:input label="Address" placeholder="Location address"
                        wire:model.live.debounce.500ms="address" />

                    {{-- Phone --}}
                    <flux:input label="Phone" placeholder="Phone number" wire:model.live.debounce.500ms="phone" />


                    <div class="flex gap-2">

                        <flux:spacer />

                        {{-- Cancel --}}
                        <flux:modal.close>
                            <flux:button variant="ghost">
                                Cancel
                            </flux:button>
                        </flux:modal.close>


                        {{-- Save --}}
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                            Save changes
                        </flux:button>

                    </div>

                </div>

            </form>

        </flux:modal>

        <!-- Modal delete -->
        <flux:modal name="deleteLocation" class="min-w-[22rem]">

            <form wire:submit.prevent="deleteLocation">

                <div class="space-y-6">

                    <div>
                        <flux:heading size="lg" class="text-red-500">
                            Delete Location
                        </flux:heading>

                        <flux:text class="mt-2">
                            Are you sure you want to delete this location?
                            This action cannot be undone.
                        </flux:text>
                    </div>

                    <div class="flex gap-2">
                        <flux:spacer />

                        <flux:modal.close>
                            <flux:button variant="ghost">
                                Cancel
                            </flux:button>
                        </flux:modal.close>

                        <flux:button type="submit" variant="danger">
                            Delete
                        </flux:button>

                    </div>

                </div>

            </form>

        </flux:modal>
    </div>

</div>
