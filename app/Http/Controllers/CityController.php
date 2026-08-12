<?php

namespace App\Http\Controllers;

use App\Cities\Banskabystrica\Banskabystrica;
use App\Cities\Hlohovec\Hlohovec;
use App\Cities\Poprad\Poprad;
use App\Cities\Presov\Presov;
use App\Cities\Trencin\Trencin;
use App\Cities\Zilina\Zilina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public function listcities()
    {
        $cities = collect([
            Banskabystrica::class,
            Hlohovec::class,
            Poprad::class,
            Presov::class,
            Trencin::class,
            Zilina::class
        ])->map(function ($class) {
            return new $class;
        });
        return view('listcities', [
            'cities' => $cities
        ]);
    }

    public function stats() {
        $types = DB::select("select count(b.id) as count, c.name from boards as b join categories as c on c.id = b.category_id group by b.category_id order by c.name");
        $createdByMonth = DB::select("select count(b.id) as count, concat(month(b.created_at), '.', year(b.created_at)) as month from boards as b where b.created_at > ? group by concat(month(b.created_at), '.', year(b.created_at)) order by b.created_at", [dateTimeNow('-2 years')]);
        $categories = DB::select("select count(cc.id) as count, c.name from cityzen_categories as cc join categories as c on c.id = cc.category_id");

        return view('stats', [
            'types' => $types,
            'createdByMonth' => $createdByMonth,
            'categories' => $categories
        ]);
    }
}
