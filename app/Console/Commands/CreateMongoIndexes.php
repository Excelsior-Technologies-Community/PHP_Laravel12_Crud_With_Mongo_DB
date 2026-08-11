<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMongoIndexes extends Command
{
    protected $signature = 'db:indexes';
    protected $description = 'Create MongoDB indexes for faster search';

    public function handle(): void
    {
        $this->info('Creating MongoDB indexes...');

        $this->createBookIndexes();
        $this->createCategoryIndexes();
        $this->createTagIndexes();
        $this->createUserIndexes();

        $this->info('All indexes created successfully.');
    }

    private function createBookIndexes(): void
    {
        $collection = $this->getCollection('books');

        $collection->createIndex(['name' => 'text', 'detail' => 'text'], ['name' => 'books_text_search', 'default_language' => 'english']);
        $collection->createIndex(['status' => 1], ['name' => 'books_status_idx']);
        $collection->createIndex(['category_id' => 1], ['name' => 'books_category_idx']);
        $collection->createIndex(['user_id' => 1], ['name' => 'books_user_idx']);
        $collection->createIndex(['created_at' => -1], ['name' => 'books_created_at_idx']);
        $collection->createIndex(['deleted_at' => 1], ['name' => 'books_deleted_at_idx']);
        $collection->createIndex(['name' => 1], ['name' => 'books_name_idx']);

        $this->line('  <fg=green>books indexes created</>');
    }

    private function createCategoryIndexes(): void
    {
        $collection = $this->getCollection('categories');

        $collection->createIndex(['slug' => 1], ['name' => 'categories_slug_idx', 'unique' => true]);
        $collection->createIndex(['name' => 'text'], ['name' => 'categories_text_search']);

        $this->line('  <fg=green>categories indexes created</>');
    }

    private function createTagIndexes(): void
    {
        $collection = $this->getCollection('tags');

        $collection->createIndex(['slug' => 1], ['name' => 'tags_slug_idx', 'unique' => true]);
        $collection->createIndex(['name' => 'text'], ['name' => 'tags_text_search']);

        $this->line('  <fg=green>tags indexes created</>');
    }

    private function createUserIndexes(): void
    {
        $collection = $this->getCollection('users');

        $collection->createIndex(['email' => 1], ['name' => 'users_email_idx', 'unique' => true]);

        $this->line('  <fg=green>users indexes created</>');
    }

    private function getCollection(string $collectionName)
    {
        /** @var \MongoDB\Laravel\Connection $connection */
        $connection = DB::connection('mongodb');

        return $connection->getCollection($collectionName);
    }
}
