<div>

    <flux:modal name="addBook" class="w-full max-w-[650px]">

        <form wire:submit.prevent="submitForm" class="space-y-6">

            <div>

                <flux:heading size="lg">
                    {{ $thisUpdateMode ? 'Edit Book' : 'Add New Book' }}
                </flux:heading>

                <flux:text class="mt-2">
                    Quản lý sách trong hệ thống
                </flux:text>

            </div>

            <div class="grid grid-cols-2 gap-6">

                {{-- IMAGE --}}
                <div>

                    <flux:label>Ảnh sách</flux:label>

                    <flux:file-upload wire:model="image" class="mt-2">
                        <flux:file-upload.dropzone heading="Kéo ảnh vào đây hoặc bấm để chọn"
                            text="PNG, JPG tối đa 10MB" />
                    </flux:file-upload>

                    @if ($image)
                        <div class="mt-4">

                            <flux:file-item heading="{{ $image->getClientOriginalName() }}"
                                image="{{ $image->temporaryUrl() }}">

                                <x-slot name="actions">
                                    <flux:file-item.remove wire:click="removeImage" />
                                </x-slot>

                            </flux:file-item>

                        </div>
                        {{-- ✅ ẢNH CŨ --}}
                    @elseif ($oldImage)
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . $oldImage) }}"
                                class="w-32 h-32 object-cover rounded border">
                        </div>
                    @endif

                </div>

                {{-- NAME --}}
                <flux:input label="Tên sách" placeholder="Nhập tên sách" wire:model.live.debounce.500ms="name" />

                {{-- AUTHOR --}}
                <flux:input label="Tác giả" placeholder="Nhập tên tác giả" wire:model.live.debounce.500ms="author" />

                {{-- YEAR --}}
                <flux:select wire:model.live.debounce.500ms="published_year" label="Năm xuất bản">

                    <option value="">Chọn năm</option>

                    @for ($year = date('Y'); $year >= 1900; $year--)
                        <option value="{{ $year }}">
                            {{ $year }}
                        </option>
                    @endfor

                </flux:select>

                {{-- LOCATION --}}
                <flux:select label="Cơ sở" wire:model.live.debounce.500ms="location_id">

                    <option value="">Chọn cơ sở</option>

                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">
                            {{ $location->name }}
                        </option>
                    @endforeach

                </flux:select>

                {{-- TYPE --}}
                <div>

                    <flux:label>Thể loại</flux:label>

                    <flux:dropdown class="mt-2 w-full">

                        {{-- BUTTON --}}
                        <flux:button icon:trailing="chevron-down" class="w-full justify-between">
                            {{ $selectedType ?: 'Chọn thể loại' }}
                        </flux:button>

                        {{-- MENU --}}
                        <flux:menu>

                            <flux:menu.item wire:click="selectType('Programming')">
                                @if (in_array('Programming', $type))
                                    ✔
                                @endif
                                Programming
                            </flux:menu.item>

                            <flux:menu.item wire:click="selectType('Business')">
                                @if (in_array('Business', $type))
                                    ✔
                                @endif
                                Business
                            </flux:menu.item>

                            <flux:menu.item wire:click="selectType('AI')">
                                @if (in_array('AI', $type))
                                    ✔
                                @endif
                                AI
                            </flux:menu.item>


                        </flux:menu>

                    </flux:dropdown>

                </div>

            </div>

            <div class="flex pt-4">

                <flux:spacer />

                <flux:button type="submit" variant="primary">
                    {{ $thisUpdateMode ? 'Update' : 'Save' }}
                </flux:button>


            </div>

        </form>

    </flux:modal>

    <flux:modal name="deleteBook" class="min-w-[22rem]">

        <form wire:submit.prevent="confirmDeleteBook">

            <div class="space-y-6">

                <div>
                    <flux:heading size="lg" class="text-red-500">
                        Delete Book
                    </flux:heading>

                    <flux:text class="mt-2">
                       Bạn có chắc chắn muốn xóa sách này không?
                        Hành động này không thể thu hồi.
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">
                            Từ từ
                        </flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="danger">
                        Xóa
                    </flux:button>

                </div>

            </div>

        </form>

    </flux:modal>

</div>
