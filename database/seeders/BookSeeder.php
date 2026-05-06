<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::create([
            'image' => 'books/laravel.jpg',
            'name' => 'Laravel Mastery',
            'type' => ['Programming', 'Laravel', 'Backend'],
            'author' => 'Taylor Otwell',
            'published_year' => 2023,
            'location_id' => 1,
        ]);
        Book::create([
            'image' => 'books/ai.jpg',
            'name' => 'Artificial Intelligence Basics',
            'type' => ['AI', 'Technology'],
            'author' => 'Andrew Ng',
            'published_year' => 2022,
            'location_id' => 1,
        ]);

        Book::create([
            'image' => 'books/startup.jpg',
            'name' => 'The Lean Startup',
            'type' => ['Business', 'Startup'],
            'author' => 'Eric Ries',
            'published_year' => 2019,
            'location_id' => 7,
        ]);

        Book::create([
            'image' => 'books/psychology.jpg',
            'name' => 'Thinking, Fast and Slow',
            'type' => ['Psychology', 'Behavioral Science'],
            'author' => 'Daniel Kahneman',
            'published_year' => 2011,
            'location_id' => 6,
        ]);

        Book::create([
            'image' => 'books/marketing.jpg',
            'name' => 'Contagious: Why Things Catch On',
            'type' => ['Marketing', 'Business'],
            'author' => 'Jonah Berger',
            'published_year' => 2013,
            'location_id' => 3,
        ]);

        Book::create([
            'image' => 'books/finance.jpg',
            'name' => 'Rich Dad Poor Dad',
            'type' => ['Finance', 'Personal Development'],
            'author' => 'Robert Kiyosaki',
            'published_year' => 1997,
            'location_id' => 5,
        ]);

    }
}
