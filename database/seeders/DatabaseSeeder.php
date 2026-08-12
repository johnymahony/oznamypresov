<?php

namespace Database\Seeders;

use App\Helpers\FakerGeoPresov;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Ivan Ivanco',
            'email' => 'ivan.ivanco@gmail.com',
            'password' => '$2y$10$KXrVMpjNNSi.N4JWzc9xNu8EfSi8ZJSg18CsUQGjGnIHgV0gOP9vy',
        ]);

        if (app()->environment() !== 'local') {
            return;
        }

        $category = \App\Category::factory()->create();
        \App\Board::factory()->create(['category_id' => 1, 'id' => 10000]);

        for ($i = 0; $i < 5; $i++) {
            $category = \App\Category::factory()->create();
            for ($ii = 0; $ii < 40; $ii++) {
                $board = \App\Board::factory()->create(['category_id' => $category->id]);
                $point = FakerGeoPresov::point();
                $board->points()->create([
                    'latitude' => $point[FakerGeoPresov::LAT],
                    'longitude' => $point[FakerGeoPresov::LNG],
                ]);
            }
        }

        \App\Category::factory()->create(['id' => 11, 'name' => 'Podnety', 'slug' => 'podnety']);

        DB::table('users')->insert([
            'name' => 'Richard Drutarovský',
            'email' => 'drutarovsky.poslanec@gmail.com',
            'password' => '$2y$10$KXrVMpjNNSi.N4JWzc9xNu8EfSi8ZJSg18CsUQGjGnIHgV0gOP9vy',
        ]);

        $uliceGroup = \Conner\Tagging\Model\TagGroup::create([
            'slug' => 'ulice',
            'name' => 'Ulice',
        ]);
        $kategorieGroup = \Conner\Tagging\Model\TagGroup::create([
            'slug' => 'kategorie',
            'name' => 'Kategorie',
        ]);

        foreach (city()->getStreetNames() as $ulica) {
            \Conner\Tagging\Model\Tag::create([
                'tag_group_id' => $uliceGroup->id,
                'name' => $ulica,
                'suggest' => true,
                'count' => 0,
            ]);

            \App\StreetName::firstOrCreate(['name' => $ulica]);
        }

        $tags = [
            'Šport', 'Radnica', 'Stavebný úrad', 'Kultúra',
            'Doprava', 'Výrub stromov', 'Mestské zastupiteľstvo',
        ];

        foreach ($tags as $label) {
            \Conner\Tagging\Model\Tag::create([
                'tag_group_id' => $kategorieGroup->id,
                'name' => $label,
                'suggest' => true,
                'count' => 0,
            ]);
        }

        for ($i = 0; $i < 15; $i++) {
            \App\Project::factory()->create([
                'phase_id' => rand(1, 8) === 7 ? null : rand(1, 8),
            ]);
        }
    }
}
