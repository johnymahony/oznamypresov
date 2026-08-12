<?php

namespace App\Cruncher;


use App\Attachment;

interface Cruncher
{
    /**
     * Get number of all uploaded documents.
     */
    public function count(): int;

    /**
     * Get a single document by id.
     */
    public function get(string $id);

    /**
     * Search documents by `phrase`.
     */
    public function search(string $phrase, $fuzzy = false): array;

    /**
     * Get all documents.
     */
    public function all(): array;

    /**
     * Delete all documents.
     */
    public function clear(): void;

    /**
     * Upload a file.
     */
    public function add(Attachment $file);

    public function createEnv($name);
}
