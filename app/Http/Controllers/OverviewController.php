<?php

namespace App\Http\Controllers;

use App\Board;
use App\Category;
use App\CookieId;
use App\TrackedBoard;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    public function map()
    {
        $rok = request()->rok;
        $categoryParts = explode('-', request()->kategoria);
        array_shift($categoryParts);
        $category = implode('-', $categoryParts);

        return redirect("/filter/$rok/$category", 301);
    }

    public function board(Board $board, $slug)
    {
        if ($cityzenId = request()->get('t')) {
            TrackedBoard::firstOrCreate([
                'cityzen_id' => $cityzenId,
                'board_id' => $board->id
            ]);
            CookieId::setCityzen($cityzenId);
        }

        if ($board->deleted) {
            dd('Oznam bol zmazany');
        }
        $board->category_name = $board->category->name;

        return view($board->category_id === city()->podnetyCategoryId() ? 'podnet' : 'detail', ['board' => $board]);
    }

}