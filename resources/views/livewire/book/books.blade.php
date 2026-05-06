<div> <!-- SLIDER -->
    <!-- SLIDER -->
    <div x-data="{
        active: 0,
        slides: [
            '/images/slider/slide1.png',
            '/images/slider/slide2.png',
            '/images/slider/slide3.png'
        ],
        start() {
            setInterval(() => {
                this.active = (this.active + 1) % this.slides.length
            }, 3000)
        }
    }" x-init="start()" class="relative w-full h-64 overflow-hidden rounded-xl mb-6">

        <template x-for="(slide, index) in slides" :key="index">

            <img x-show="active === index" x-transition :src="slide"
                class="absolute inset-0 w-full h-full object-cover">

        </template>

    </div>

    <livewire:book.actions-book :locations="$this->locations" />

    <div class="p-4">

        {{-- FILTER --}}
        <div class="flex items-center gap-4 mb-4">

            {{-- SEARCH --}}
            <flux:input wire:model.live="search" placeholder="Tìm tên sách..." icon="magnifying-glass" class="w-64" />

            {{-- LOCATION FILTER --}}
            <flux:dropdown>

                <flux:button icon:trailing="chevron-down">
                    {{ $locationFilter && $this->locations->find($locationFilter)
                        ? $this->locations->find($locationFilter)->name
                        : 'Cơ sở' }}
                </flux:button>

                <flux:menu>
                    @foreach ($this->locations as $location)
                        <flux:menu.item wire:click="$set('locationFilter', {{ $location->id }})">
                            {{ $location->name }}
                        </flux:menu.item>
                    @endforeach

                    <flux:menu.item wire:click="$set('locationFilter', '')">
                        Tất cả
                    </flux:menu.item>
                </flux:menu>

            </flux:dropdown>

            {{-- TYPE FILTER --}}
            <flux:dropdown>

                <flux:button icon:trailing="chevron-down">
                    {{ $typeFilter ?: 'Thể loại' }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="$set('typeFilter','Programming')">
                        Programming
                    </flux:menu.item>

                    <flux:menu.item wire:click="$set('typeFilter','AI')">
                        AI
                    </flux:menu.item>

                    <flux:menu.item wire:click="$set('typeFilter','Business')">
                        Business
                    </flux:menu.item>

                    <flux:menu.item wire:click="$set('typeFilter','')">
                        Tất cả
                    </flux:menu.item>
                </flux:menu>

            </flux:dropdown>

            {{-- ADD BUTTON --}}
            @can('book.create')
                <flux:button variant="primary" icon="plus" wire:click="addBook">
                    Thêm sách
                </flux:button>
            @endcan


        </div>



        {{-- TABLE --}}
        <flux:table :paginate="$this->books">

            <flux:table.columns>
                <flux:table.column>Book</flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'author'" :direction="$sortDirection"
                    wire:click="sort('author')">
                    Author
                </flux:table.column>

                <flux:table.column sortable :sorted="$sortBy === 'published_year'" :direction="$sortDirection"
                    wire:click="sort('published_year')">
                    Year
                </flux:table.column>

                <flux:table.column>Type</flux:table.column>

                @canany(['book.update', 'book.delete'])
                      <flux:table.column>Action</flux:table.column>
                @endcanany

            </flux:table.columns>

            <flux:table.rows>

                @if ($this->books->count() == 0)

                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-10 text-zinc-400">

                            @if ($locationFilter && $typeFilter)
                                Không có sách thuộc cơ sở này và thể loại này
                            @elseif ($locationFilter)
                                Không có sách ở cơ sở này
                            @elseif ($typeFilter)
                                Không có sách thuộc thể loại này
                            @else
                                Chưa có sách nào trong hệ thống
                            @endif

                        </flux:table.cell>
                    </flux:table.row>
                @else
                    @foreach ($this->books as $book)
                        <flux:table.row :key="$book->id">

                            {{-- BOOK --}}
                            <flux:table.cell class="flex items-center gap-3">

                                <img src="{{ $book->image_url }}" class="w-10 h-14 object-cover rounded shadow">

                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $book->name }}</span>
                                    <span class="text-xs text-zinc-400">#
                                        {{ $this->books->firstItem() ? $this->books->firstItem() + $loop->index : '' }}</span>
                                </div>

                            </flux:table.cell>

                            {{-- AUTHOR --}}
                            <flux:table.cell>
                                {{ $book->author }}
                            </flux:table.cell>

                            {{-- YEAR --}}
                            <flux:table.cell>
                                {{ $book->published_year }}
                            </flux:table.cell>

                            {{-- TYPE --}}
                            <flux:table.cell>

                                @php
                                    $types = [];
                                    if (!empty($book->type)) {
                                        $types = is_array($book->type) ? $book->type : explode(',', $book->type);
                                    }
                                @endphp

                                <div class="flex flex-wrap gap-1">
                                    @foreach ($types as $t)
                                        <flux:badge size="sm" color="blue">
                                            {{ trim($t) }}
                                        </flux:badge>
                                    @endforeach
                                </div>

                            </flux:table.cell>

                            {{-- ACTION --}}
                            <flux:table.cell>
                                <div class="flex gap-1">
                                    @can('book.update')
                                        <flux:button size="sm" variant="ghost" icon="pencil"
                                            wire:click="$dispatch('editBook', { id: {{ $book->id }} })" />
                                    @endcan

                                    @can('book.delete')
                                        <flux:button size="sm" variant="ghost" icon="trash"
                                            wire:click="$dispatch('deleteBook', { id: {{ $book->id }} })" />
                                    @endcan
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                    @endforeach

                @endif

            </flux:table.rows>

        </flux:table>

    </div>

</div>
