<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMongoIndexes extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:indexes';

    /**
     * The console command description.
     */
    protected $description = 'Create MongoDB indexes for faster search';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating MongoDB indexes...');
        $this->newLine();

        try {
            $this->createBookIndexes();
            $this->createCategoryIndexes();
            $this->createTagIndexes();
            $this->createUserIndexes();
            $this->createBorrowingIndexes();

            $this->newLine();
            $this->info('All MongoDB indexes are ready.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('Failed to create MongoDB indexes.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Create a normal MongoDB index only when an equivalent
     * index does not already exist.
     */
    private function createIndex(
        string $collection,
        array $keys,
        string $name,
        array $options = []
    ): void {
        $mongoCollection = DB::connection('mongodb')
            ->getCollection($collection);

        /*
         * Check all existing indexes.
         *
         * We compare the actual index keys instead of only
         * comparing the index name.
         *
         * Example:
         *
         * Existing:
         *     email_1
         *
         * Requested:
         *     users_email_idx
         *
         * Both use:
         *     ['email' => 1]
         *
         * Therefore we don't try to create another index.
         */
        foreach ($mongoCollection->listIndexes() as $existingIndex) {
            $existingKeys = $existingIndex->getKey();

            if ($existingKeys == $keys) {
                $existingName = $existingIndex->getName();

                $this->line(
                    "  <fg=yellow>{$collection}.{$name}</> already exists " .
                    "(using {$existingName})"
                );

                return;
            }
        }

        /*
         * No equivalent index was found,
         * so create the requested index.
         */
        $mongoCollection->createIndex(
            $keys,
            array_merge(
                [
                    'name' => $name,
                ],
                $options
            )
        );

        $this->line(
            "  <fg=green>{$collection}.{$name} created</>"
        );
    }

    /**
     * Create books collection indexes.
     */
    private function createBookIndexes(): void
    {
        /*
         * Title index.
         */
        $this->createIndex(
            'books',
            [
                'title' => 1,
            ],
            'books_title_idx'
        );

        /*
         * Author index.
         */
        $this->createIndex(
            'books',
            [
                'author' => 1,
            ],
            'books_author_idx'
        );

        /*
         * Category index.
         */
        $this->createIndex(
            'books',
            [
                'category_id' => 1,
            ],
            'books_category_idx'
        );

        /*
         * Created date index.
         */
        $this->createIndex(
            'books',
            [
                'created_at' => -1,
            ],
            'books_created_at_idx'
        );

        /*
         * MongoDB allows only ONE text index
         * per collection.
         *
         * Your database already contains:
         *
         *     books_text_search
         *
         * Therefore we must NOT try to create
         * another text index.
         */
        $this->ensureTextIndex('books');
    }

    /**
     * Ensure that the books collection has a text index.
     *
     * If a text index already exists, use it.
     *
     * If no text index exists, create one using
     * the fields used by this project.
     */
    private function ensureTextIndex(string $collection): void
    {
        $mongoCollection = DB::connection('mongodb')
            ->getCollection($collection);

        /*
         * Look for an existing text index.
         *
         * MongoDB represents a text index internally
         * using the "_fts" key.
         */
        foreach ($mongoCollection->listIndexes() as $existingIndex) {
            $existingKeys = $existingIndex->getKey();

            if (
                isset($existingKeys['_fts']) &&
                $existingKeys['_fts'] === 'text'
            ) {
                $existingName = $existingIndex->getName();

                $this->line(
                    "  <fg=yellow>{$collection} text index already exists " .
                    "(using {$existingName})</>"
                );

                return;
            }
        }

        /*
         * No text index exists.
         *
         * Create one using the fields that already exist
         * in the original project.
         */
        $mongoCollection->createIndex(
            [
                'name' => 'text',
                'detail' => 'text',
            ],
            [
                'name' => 'books_text_search',
            ]
        );

        $this->line(
            "  <fg=green>{$collection}.books_text_search created</>"
        );
    }

    /**
     * Create categories collection indexes.
     */
    private function createCategoryIndexes(): void
    {
        /*
         * Category name.
         */
        $this->createIndex(
            'categories',
            [
                'name' => 1,
            ],
            'categories_name_idx',
            [
                'unique' => true,
            ]
        );

        /*
         * Category slug.
         */
        $this->createIndex(
            'categories',
            [
                'slug' => 1,
            ],
            'categories_slug_idx',
            [
                'unique' => true,
            ]
        );
    }

    /**
     * Create tags collection indexes.
     */
    private function createTagIndexes(): void
    {
        /*
         * Tag name.
         */
        $this->createIndex(
            'tags',
            [
                'name' => 1,
            ],
            'tags_name_idx',
            [
                'unique' => true,
            ]
        );

        /*
         * Tag slug.
         */
        $this->createIndex(
            'tags',
            [
                'slug' => 1,
            ],
            'tags_slug_idx',
            [
                'unique' => true,
            ]
        );
    }

    /**
     * Create users collection indexes.
     */
    private function createUserIndexes(): void
    {
        /*
         * Email index.
         *
         * Your database already has:
         *
         *     email_1
         *
         * with:
         *
         *     ['email' => 1]
         *
         * createIndex() detects that and won't
         * attempt to create another equivalent index.
         */
        $this->createIndex(
            'users',
            [
                'email' => 1,
            ],
            'users_email_idx',
            [
                'unique' => true,
            ]
        );
    }

    /**
     * Create borrowings collection indexes.
     */
    private function createBorrowingIndexes(): void
    {
        /*
         * User ID.
         */
        $this->createIndex(
            'borrowings',
            [
                'user_id' => 1,
            ],
            'borrowings_user_idx'
        );

        /*
         * Book ID.
         */
        $this->createIndex(
            'borrowings',
            [
                'book_id' => 1,
            ],
            'borrowings_book_idx'
        );

        /*
         * Borrowing status.
         */
        $this->createIndex(
            'borrowings',
            [
                'status' => 1,
            ],
            'borrowings_status_idx'
        );

        /*
         * Borrowed date.
         */
        $this->createIndex(
            'borrowings',
            [
                'borrowed_at' => -1,
            ],
            'borrowings_borrowed_at_idx'
        );

        /*
         * User + status compound index.
         */
        $this->createIndex(
            'borrowings',
            [
                'user_id' => 1,
                'status' => 1,
            ],
            'borrowings_user_status_idx'
        );

        /*
         * Book + status compound index.
         */
        $this->createIndex(
            'borrowings',
            [
                'book_id' => 1,
                'status' => 1,
            ],
            'borrowings_book_status_idx'
        );
    }
}