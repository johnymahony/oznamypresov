<?php

use App\Helpers\FakerGeoPresov;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Ivan Ivanco',
            'email' => 'ivan.ivanco@gmail.com',
            'password' => '$2y$10$KXrVMpjNNSi.N4JWzc9xNu8EfSi8ZJSg18CsUQGjGnIHgV0gOP9vy',
        ]);
        if (app()->environment() !== 'local') {
            return;
        }
        $category = factory(\App\Category::class)->create();
        factory(\App\Board::class)->create(['category_id' => 1, 'id' => 10000]);

        for ($i = 0; $i < 5; $i++) {
            $category = factory(\App\Category::class)->create();
            for ($ii = 0; $ii < 40; $ii++) {
                $board = factory(\App\Board::class)->create(['category_id' => $category->id]);
                $point = FakerGeoPresov::point();
                $board->points()->create([
                    'latitude' => $point[FakerGeoPresov::LAT],
                    'longitude' => $point[FakerGeoPresov::LNG]
                ]);
            }
        }
        $category = factory(\App\Category::class)->create(['id' => 11, 'name' => 'Podnety', 'slug' => 'podnety']);


        DB::table('users')->insert([
            'name' => 'Richard Drutarovský',
            'email' => 'drutarovsky.poslanec@gmail.com',
            'password' => '$2y$10$KXrVMpjNNSi.N4JWzc9xNu8EfSi8ZJSg18CsUQGjGnIHgV0gOP9vy',
        ]);

        $uliceGroup = \Conner\Tagging\Model\TagGroup::create([
            'slug' => 'ulice',
            'name' => 'Ulice'
        ]);
        $kategorieGroup = \Conner\Tagging\Model\TagGroup::create([
            'slug' => 'kategorie',
            'name' => 'Kategorie'
        ]);

        foreach (city()->getStreetNames() as $ulica) {
            $tag = \Conner\Tagging\Model\Tag::create([
                'tag_group_id' => $uliceGroup->id,
                'name' => $ulica,
                'suggest' => true,
                'count' => 0
            ]);

            \App\StreetName::firstOrCreate([
                'name' => $ulica,
            ]);
        }

        $tags = [
            'Šport',
            'Radnica',
            'Stavebný úrad',
            'Kultúra',
            'Doprava',
            'Výrub stromov',
            'Mestské zastupiteľstvo',
        ];

        foreach ($tags as $label) {
            $tag = \Conner\Tagging\Model\Tag::create([
                'tag_group_id' => $kategorieGroup->id,
                'name' => $label,
                'suggest' => true,
                'count' => 0
            ]);
        }

        for ($i = 0; $i < 15; $i++) {
            factory(\App\Project::class)->create([
                'phase_id' => rand(1, 8) === 7 ? null : rand(1, 8)
            ]);//kazdy siedmy nullovy
        }
    }
}
