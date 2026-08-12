<?php

namespace App\Http\Controllers;

use App\CityzenBoard;
use App\TrackedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function emailsOpended()
    {
        $data = TrackedEmail::
        select(
            DB::raw("(select count(t2.id) from tracked_emails as t2 where t2.blade = tracked_emails.blade and date(t2.date_sent) = DATE(tracked_emails.date_sent)) as count_sent"),
            DB::raw("(select count(a.id) from tracked_attachments as a where date(a.created_at) = DATE(tracked_emails.date_sent)) as count_attachments_opened"),
            DB::raw("count(*) as count_opened"),
            DB::raw("avg(TIMESTAMPDIFF(MINUTE, date_sent, updated_at)) as avg"),
            DB::raw("date(date_sent) as date_sent"),
            'blade'
        )
                            ->groupBy(DB::raw("date(date_sent)"))
                            ->groupBy("blade")
                            ->whereColumn('created_at', '!=', 'updated_at')
                            ->orderBy('date_sent', 'desc')
                            ->orderBy('blade')
                            ->get();

        return view('reports.emails', ['data' => $data]);
    }

    public function boardsSent()
    {
        $data = CityzenBoard::select(
                    DB::raw("count(*) as count"),
                    'board_id',
                    'published_at',
                    'description',
                    'title',
                    DB::raw("(select count(*) from tracked_boards as tb where tb.board_id = cityzen_boards.board_id)  as count_opened_board "),
                    DB::raw("(select count(*) from tracked_attachments as ta JOIN attachments as a on a.id = ta.attachment_id where a.board_id = cityzen_boards.board_id)  as count_opened_attachments ")
    )
                            ->join('boards', 'boards.id', '=',
                                'cityzen_boards.board_id')
                            ->groupBy('board_id')
                            ->orderBy('boards.published_at', 'desc')->get();

        return view('reports.boards_sent', ['data' => $data]);
    }
}
