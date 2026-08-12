<?php

namespace App\Cruncher;

use App\Attachment;

use Illuminate\Support\Str;

class NullDriver implements Cruncher
{
    public function count(): int
    {
        return 0;
    }

    public function get(string $id)
    {
        return (object)[];
    }

    public function search(string $phrase, $fuzzy = false): array
    {
        return [];
    }

    public function all(): array
    {
        return [];
    }

    public function clear(): void
    {
        //
    }

    public function add(Attachment $file)
    {
        return (object)[
            'id' => Str::random(),
        ];
    }

    public function createEnv($name, $s3Bucket = null)
    {
    }
}
