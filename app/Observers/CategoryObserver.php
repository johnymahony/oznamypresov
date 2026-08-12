<?php

namespace App\Observers;

use App\Category;
use App\Cityzen;
use App\Log;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 6. 8. 2018
 * Time: 14:31
 */
class CategoryObserver
{
    public function created(Category $category)
    {
        Log::debug("Category created " . $category->name);
        $category->slug = toSlug($category->name);
        $category->save();
        $category->addToSitemap();
        $cityzens = Cityzen::whereNotNull('email')->whereNotNull('radius')
                           ->get();
        foreach ($cityzens as $cityzen) {
            $cityzen->sendEmail('new_category', ['category' => $category]);
            DB::table('cityzen_categories')->insert([
                'cityzen_id'  => $cityzen->id,
                'category_id' => $category->id,
                'created_at'  => dateTimeNow(),
                'updated_at'  => dateTimeNow()
            ]);
            //$cityzen->categories()->attach($category);
        }
    }
}