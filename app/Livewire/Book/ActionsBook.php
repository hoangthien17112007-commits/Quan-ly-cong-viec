<?php

namespace App\Livewire\Book;

use App\Models\Book;
use App\Models\Location;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class ActionsBook extends Component
{
    use WithFileUploads;

    public $image;
    public $oldImage;
    public $bookId;
    public $name;
    public $author;
    public $published_year;
    public $location_id;
    public $type = [];
    public $selectedType;
    public $locations = [];

    // 👉 dùng chung add/edit
    public $thisUpdateMode = false;

    // RULES
    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'author' => 'required|min:3|max:255',
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'type' => 'required|array|min:1',
            'location_id' => 'required|exists:locations,id',
            'image' => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'name.required' => 'Vui lòng nhập tên sách',
        'author.required' => 'Vui lòng nhập tác giả',
        'published_year.required' => 'Vui lòng nhập năm',
        'type.required' => 'Chọn ít nhất 1 thể loại',
        'location_id.required' => 'Chọn cơ sở',
    ];

    public function mount($locations = null)
    {
        $this->locations = $locations ?: Location::query()->get(['id', 'name']);
    }

    // public function updated($property)
    //{
    // $this->validateOnly($property);
    // }

    // chọn type
    public function selectType($type)
    {
        if (in_array($type, $this->type)) {
            $this->type = array_values(array_diff($this->type, [$type]));
        } else {
            $this->type[] = $type;
        }

        $this->selectedType = implode(', ', $this->type);
        $this->validateOnly('type');
    }

    public function removeImage()
    {
        $this->image = null;
    }

    public function submitForm()
    {
        ('hehe');
        if ($this->thisUpdateMode) {
            $this->updateBook();
        } else {
            $this->createBook();
        }
    }

    // ================== ADD ==================
    #[On('addBook')]
    public function addBook()
    {
        $this->authorize('book.create');
        $this->resetForm();
        $this->thisUpdateMode = false;

        Flux::modal('addBook')->show(); // 👉 dùng chung modal
    }

    public function createBook()
    {
        $this->validate();

        $imagePath = $this->image
            ? $this->image->store('books', 'public')
            : null;

        Book::create([
            'name' => $this->name,
            'author' => $this->author,
            'published_year' => $this->published_year,
            'type' => $this->type,
            'location_id' => $this->location_id,
            'image' => $imagePath,
        ]);

        $this->afterSave('bookUpdated');
        Flux::toast(
            text: 'Sách đã được tạo!',
            heading: 'Thành công',
            variant: 'success',
            duration: 3000
        );
        $this->dispatch("play-success");
    }

    // ================== EDIT ==================
    #[On('editBook')]
    public function editBook($id)
    {
        $book = Book::findOrFail($id);

        $this->bookId = $book->id;
        $this->name = $book->name;
        $this->author = $book->author;
        $this->published_year = $book->published_year;
        $this->type = $book->type;
        $this->selectedType = implode(', ', $book->type);
        $this->location_id = $book->location_id;
        $this->oldImage = $book->image;

        $this->image = null; // reset image mới khi edit

        $this->thisUpdateMode = true;

        Flux::modal('addBook')->show(); // 👉 dùng chung modal
    }

    public function updateBook()
    {
        //$this->validate();

        $book = Book::findOrFail($this->bookId);

        $imagePath = $this->oldImage;

        if ($this->image) {
            $imagePath = $this->image->store('books', 'public');
        }

        $book->update([
            'name' => $this->name,
            'author' => $this->author,
            'published_year' => $this->published_year,
            'type' => $this->type,
            'location_id' => $this->location_id,
            'image' => $imagePath,
        ]);

        $this->afterSave('bookUpdated');
    }

    #[On('deleteBook')]
    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $this->bookId = $book->id;
        Flux::modal('deleteBook')->show();
    }
    public function confirmDeleteBook()
    {
        $book = Book::findOrFail($this->bookId);
        $book->delete();

        Flux::modal('deleteBook')->close();

       $this->afterSave('bookUpdated');
            Flux::toast(
            text: 'Location đã được xóa!',
            heading: 'Thành công',
            variant: 'danger',
            duration: 3000
        );
        $this->dispatch('play-sound', 'delete');
    }

    // ================== COMMON ==================
    private function afterSave($event)
    {
        $this->resetForm();
        $this->resetErrorBag();

        Flux::modal('addBook')->close();

        $this->dispatch($event);
    }

    private function resetForm()
    {
        $this->reset([
            'name',
            'author',
            'published_year',
            'location_id',
            'type',
            'selectedType',
            'image',
            'oldImage',
            'bookId'
        ]);
    }

    public function render()
    {
        return view('livewire.book.actions-book');
    }
}
