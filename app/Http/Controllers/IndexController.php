<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Board;
use App\Category;
use App\Cityzen;
use App\Console\Commands\Scrapper;
use App\Helpers\HttpRequest;
use App\Http\Requests\SubscribeRequest;
use App\Log;
use App\Street;
use App\TrackedAttachment;
use App\Traits\Flashable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    use Flashable;

    public function index($slashData = '')
    {
        $data = explode('/', $slashData);

        $query = \App\Board::select(
            'boards.id',
            'boards.title',
            'boards.description',
            'boards.published_at',
            'boards.slug',
            'boards.latitude',
            'boards.longitude',
            'boards.attachment_path',
            'boards.status',
            'c.name as category_name'
        )
            ->join('categories as c', 'c.id', 'boards.category_id');

        $categories = [];
        $search = '';
        $year = false;
        if (@$data[0]) {
            $isSearch = ! is_numeric($data[0]) && $data[0] !== 'cele-obdobie' && ! Category::whereSlug($data[0])->first();
            if ($isSearch) {
                $search = array_shift($data);
                $query->join('attachments as a', 'a.board_id', 'boards.id');
                $query->where(function ($query) use ($search) {
                    $query->where('boards.title', 'like', '%' . $search . '%')
                        ->orWhere('boards.description', 'like', '%' . $search . '%')
                        ->orWhere('a.plain_text', 'like', '%' . $search . '%')
                        ->orWhere('a.ocr', 'like', '%' . $search . '%');
                });
            }
            if (is_numeric(@$data[0])) {
                $year = array_shift($data);
            }
            if (@$data[0] === 'cele-obdobie') {
                $year = 'cele-obdobie';
                array_shift($data);
            }
            if ( ! empty($data) && $slashData !== '') {
                $categories = $data;
                $query->whereIn('c.slug', $categories);
            }
        }
        if ($year === 'cele-obdobie') {

        } elseif ($year) {
            $query->where(DB::raw("YEAR(published_at)"), $year);
        } else {
            $query->where('published_at', '>', dateTimeNow('-1 year'));
        }
        $allQuery = clone $query;

        return view('index', [
            'allBoards' => $allQuery->select('boards.id', 'latitude',
                'longitude')->whereNotNull('boards.latitude')->get(),
            'boards' => $query->orderBy('published_at', 'desc')->paginate(40),
            'categories' => $categories,
            'year' => $year,
            'search' => $search,
            'filter' => ! empty($slashData)
        ]);

    }

    public function unsubscribe($hash)
    {
        $cityzen = Cityzen::where('hash', $hash)->first();
        if ($cityzen) {
            DB::delete('delete from cityzen_boards where cityzen_id = ?', [$cityzen->id]);
            DB::delete('delete from cookie_ids where cityzen_id = ?', [$cityzen->id]);
            DB::delete('delete from cityzen_categories where cityzen_id = ?', [$cityzen->id]);
            DB::delete('delete from cityzen_positions where cityzen_id = ?', [$cityzen->id]);
            DB::delete('delete from messages where cityzen_id = ?', [$cityzen->id]);
            DB::delete('delete from tracked_attachments where cityzen_id = ?', [$cityzen->id]);
            DB::delete('delete from tracked_boards where cityzen_id = ?', [$cityzen->id]);
            DB::delete('delete from tracked_emails where cityzen_id = ?', [$cityzen->id]);
            $cityzen->delete();
            $admin = Cityzen::where(['email' => config('app.admin_email')])->first();
            if ($admin) {
                $admin->sendEmail('unsubscribed', [
                    'cityzen' => $cityzen,
                ]);
            }
        }

        return $this->flash('unsubscribe-success');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function savefeedback()
    {
        Log::debug("Unsubscribe feedback post " . print_r($_POST, 1));
        $cityzen = Cityzen::where(['email' => config('app.admin_email')])->first();
        if ($cityzen) {
            $cityzen->sendEmail('unsubscribe_feedback', [
                'reason' => request()->get('reason'),
                'comment' => request()->get('comment'),
            ]);
        }

        return $this->flash('unsubscribe-feedback');
    }

    public function logo($blade = '', $userId = '', $time = '')
    {
        Log::debug("track logo:$blade, $userId, $time");
        if ($blade && $userId && $time) {
            \App\TrackedEmail::firstOrCreate([
                'cityzen_id' => $userId,
                'blade' => $blade,
                'date_sent' => date('Y-m-d H:i:s', $time)
            ])->update(['updated_at' => dateTimeNow()]);
        }
        header('Content-Type: image/png');
        readfile(public_path() . '/images/' . city() . '-logo.png');
    }

    public function verify($hash)
    {

        $cityzen = Cityzen::where('hash', $hash);
        if (config('app.env') !== 'local') {
            //$cityzen->where('hash_until', '>', dateTimeNow());
        }
        $cityzen = $cityzen->first();
        if (empty($cityzen)) {
            return $this->flash('link-expired');
        }
        $numbers = [];
//        if ($cityzen->street_id) {
//            $numbers = Street::where('name', $cityzen->street->name)->orderBy('cs')->get();
//        }
        $cityzen->toJavascript();

        return view('subscribe', [
            'streets' => Street::toJavascript(),
            'cityzen' => $cityzen,
            'hash' => $hash,
            'numbers' => $numbers,
            'categories' => \App\Category::orderBy('name')->get()
        ]);
    }

    /**
     * @param $hash
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function savenoaddress($hash)
    {
        $cityzen = Cityzen::where('hash', $hash)->first();
        if (empty($cityzen)) {
            return redirect('/');
        }
        Log::error("Chybajuca adresa: " . request()->street . ', ' . $cityzen);

        return $this->flash('missing-address');
    }

    public function subscribe(SubscribeRequest $request)
    {
        $result = $request->handle();
        if ($result === 'spam') {
            return '';
        }
        if ($result) {
            return 'subscribe-success';
        }

        return 'update-success';
    }

    public function download(\App\Board $board, $type = 'attachment')
    {
        if ($board->deleted) {
            dd('Oznam bol zmazany');
        }
        header("Content-Description: File Transfer");
        $filename = skDate($board->published_at) . ' - ' . trim(str_replace(["\r", "\n"], '', $board->title)) . '.pdf';
        header("Content-Disposition: $type; filename=\"" . $filename . "\"");
        header("Content-Type: application/pdf");
        Log::debug("Download attachment board: {$board->id}, $type, cityzen: " . request()->get('c', ''));
        $attachment = $board->attachments->first();
        if (request()->get('c', false)) {
            TrackedAttachment::firstOrCreate([
                'cityzen_id' => request()->get('c'),
                'attachment_id' => $attachment->id
            ]);
        }
        echo Storage::get($attachment->path);
        die();
    }

    public function ocr()
    {
        parse_str(request()->getContent(), $result);
        Log::debug("OCR result2:" . json_encode($result));
        if (isPresov() && ! preg_match("/Presov/", $result['filename'])) {
            preg_match("/\d+([a-zA-Z]*)\./", $result['filename'], $matches);
            $city = $matches[1];
            (new HttpRequest())->call(config('app.' . $city) . '/ocr', request()->getContent());

            return;

        }
        /** @var Attachment $attachment */
        $attachment = Attachment::whereBoardId(onlyNumbers($result['filename']))->first();
        if (empty($attachment)) {
            Log::debug("No attachment:" . json_encode($result));

            return;
        }

        $attachment->ocr = $result['text'];
        $attachment->plain_text = $result['text'];
        $attachment->save();
        $attachment->processText();
    }
}
