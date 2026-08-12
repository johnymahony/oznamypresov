<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 28. 9. 2020
 * Time: 6:49
 */

namespace App\Adapter;


use App\Category;
use App\Problem;
use App\StreetName;
use App\Traits\GetJsonFromCity;

class Corageo
{
    use GetJsonFromCity;

    public function getBoard($url)
    {
        $board_data = [];

        try {
            $board_rows = $this->getFromJson($url);
        } catch (\Exception $e) {
            Problem::make("Problem in JSON in scrapper: " . $e->getMessage(), null, $e . '');

            return $board_data;
        }

        foreach ($board_rows as $i => $row) {
            $row = array_map(function ($item) {
                return trim($item);
            }, $row);
            $categoryLabel = $row['Kategória_dokumentu'];
            $description = $row['Anotácia_dokumentu'];
            if (
                preg_match("/Oznámenie miesta uloženia písomnosti/", $description)
                || preg_match("/Oznámenie o doručení písomnosti/", $description)
            ) {
                $categoryLabel = 'Doručenie / uloženie písomnosti';
            }
            if (
            preg_match("/Oznámenie o zrušení trvalého pobytu/", $description)
            ) {
                $categoryLabel = 'Oznámenia o zrušení trvalého pobytu';
            }
            $categoryLabel = trim($categoryLabel);
            $category = Category::firstOrCreate(['name' => $categoryLabel]);
            $from = dateTimeNow();
            if (isset($row['Zverejnené_od'])) {
                $from = $row['Zverejnené_od'];
            }
            if (isset($row['na_ÚET_od'])) {
                $from = $row['na_ÚET_od'];
            }

            $to = dateNow('+21 days');
            if (isset($row['Zverejniť_na_UET_do'])) {
                $to = $row['Zverejniť_na_UET_do'];
            }
            if (isset($row['na_ÚET_do'])) {
                $to = $row['na_ÚET_do'];
            }
            $date = dateNow($from);
            $data = [
                'category_id' => $category->id,
                'title' => $row['Názov_dokumentu'],
                'description' => $description,
                'published_at' => $date,
                'published_uet_until' => dateNow($to),
                'link' => $row['Dokumenty'],
                'category_label' => $categoryLabel
            ];

            if (@$row['Pripomienkovať_do']) {
                $data['note_until'] = dateNow($row['Pripomienkovať_do']);
            }

            $data['slug'] = toSlug($data['description']);
            $data['po_id'] = md5(implode('', $data));

            $board_data[] = $data;
        }

        return $board_data;
    }

    public function syncStreets($url)
    {
        $list = $this->getFromJson($url);
        $streets = [];
        foreach ($list as $item) {
            $streets[] = StreetName::firstOrCreate([
                'name' => $item['Ulica'],
            ]);
        }

        return collect($streets);
    }

}