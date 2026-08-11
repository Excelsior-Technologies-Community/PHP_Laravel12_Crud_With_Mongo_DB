<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name'     => 'Admin',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Normal user
        $user = User::firstOrCreate(['email' => 'user@gmail.com'], [
            'name'     => 'John Doe',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        // Categories
        $categoryNames = ['Fiction', 'Science', 'History', 'Technology', 'Biography'];
        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[] = Category::firstOrCreate(['name' => $name]);
        }

        // Tags
        $tagNames = ['bestseller', 'classic', 'new-arrival', 'recommended', 'award-winner'];
        $tags = [];
        foreach ($tagNames as $name) {
            $tags[] = Tag::firstOrCreate(['name' => $name]);
        }

        // 10 Books
        $books = [
            ['name' => 'The Great Gatsby',        'detail' => 'A story of the wealthy Jay Gatsby and his love for Daisy Buchanan.',          'status' => 'available', 'cat' => 0, 'tag' => 0],
            ['name' => 'A Brief History of Time',  'detail' => 'Stephen Hawking explores the universe, black holes, and time.',               'status' => 'borrowed',  'cat' => 1, 'tag' => 1],
            ['name' => 'Sapiens',                  'detail' => 'A narrative history of humankind from the Stone Age to modern era.',          'status' => 'available', 'cat' => 2, 'tag' => 2],
            ['name' => 'Clean Code',               'detail' => 'A handbook of agile software craftsmanship by Robert C. Martin.',             'status' => 'available', 'cat' => 3, 'tag' => 3],
            ['name' => 'Steve Jobs',               'detail' => 'Exclusive biography of Apple co-founder Steve Jobs by Walter Isaacson.',      'status' => 'sold',      'cat' => 4, 'tag' => 4],
            ['name' => 'To Kill a Mockingbird',    'detail' => 'A novel about racial injustice and moral growth in the American South.',      'status' => 'available', 'cat' => 0, 'tag' => 1],
            ['name' => 'The Selfish Gene',         'detail' => 'Richard Dawkins introduces the gene-centered view of evolution.',             'status' => 'borrowed',  'cat' => 1, 'tag' => 2],
            ['name' => 'Dune',                     'detail' => 'Epic science fiction set in a distant future feudal interstellar society.',   'status' => 'available', 'cat' => 0, 'tag' => 0],
            ['name' => 'Elon Musk',                'detail' => 'Biography of the entrepreneur behind Tesla and SpaceX by Walter Isaacson.',   'status' => 'available', 'cat' => 4, 'tag' => 3],
            ['name' => 'The Pragmatic Programmer', 'detail' => 'Tips and philosophies for developers to improve their craft.',                'status' => 'sold',      'cat' => 3, 'tag' => 4],
        ];

        foreach ($books as $b) {
            Book::firstOrCreate(['name' => $b['name']], [
                'detail'      => $b['detail'],
                'status'      => $b['status'],
                'category_id' => $categories[$b['cat']]->_id,
                'tag_ids'     => [$tags[$b['tag']]->_id],
                'user_id'     => $user->_id,
            ]);
        }

        echo "\n✅ Seeded: 2 users, {$this->count(Category::class)} categories, {$this->count(Tag::class)} tags, " . Book::count() . " books\n";
    }

    private function count(string $model): int
    {
        return $model::count();
    }
}
