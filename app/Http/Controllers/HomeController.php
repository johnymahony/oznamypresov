<?php

namespace App\Http\Controllers;

use App\Board;
use App\Street;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function cityzens()
    {
        return view('cityzens');
    }

    public function edit(Board $board)
    {
        if ( ! empty($_POST)) {
            $tagsString = trim(request()->get('tags'));
            $tagsString = trim($tagsString, ',');
            $tags = explode(',', $tagsString);
            $currentStreets = implode(',', $board->tagNames());
            if ($tagsString !== $currentStreets) {
                $board->addManualTrace([
                    'streets' => [
                        'from' => $currentStreets,
                        'to' => $tagsString
                    ]
                ]);
            }
            if (empty($tags) || empty($tagsString)) {
                $board->untag();
            } else {
                $board->retag($tags);
            }

            $currentStatus = $board->status;
            $newStatus = request()->get('status');
            $update = [
                'status' => $newStatus,
                'cadaster_number' => request()->cadaster_number,
                'comment' => request()->comment,
                'whole_city' => request()->whole_city,
                'not_important' => request()->not_important,
                'project_id' => request()->project_id
            ];
            if ($currentStatus != $newStatus && $newStatus == Board::STATUS_DONE) {
                $update['ready_at'] = dateTimeNow();
            }
            $board->update($update);
            if (request()->get('redirect', false)) {
                return redirect('edit/' . request()->get('next_id'));
            }
        }
        if ($board->deleted) {
            dd('Oznam bol zmazany');
        }
        View::share('board', $board);
        $queryNext = Board::where('status', Board::STATUS_READY)->where('id', '!=', $board->id);
        if (auth()->user()->email !== 'ivan.ivanco@gmail.com') {
            $queryNext->whereSentDaily(false);
        }

        return view('edit', [
            'streets' => Street::select(DB::raw('distinct(name)'))->orderBy('name')->get(),
            'board' => $board,
            'next' => $queryNext->orderBy('id', 'desc')->first(),
        ]);
    }
}
