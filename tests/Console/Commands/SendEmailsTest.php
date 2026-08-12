<?php

namespace Tests\Console\Commands;

use App\Attachment;
use App\Board;
use App\Cityzen;
use App\CityzenBoard;
use App\Console\Commands\SendEmails;
use App\Mail\GeneralMailable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendEmailsTest extends TestCase
{
    use DatabaseTransactions;

    public function testHandleDontSend()
    {
        $this->runJob(10, 10);

        Mail::assertNotSent(GeneralMailable::class);
    }

    public function testHandleSendBecauseOld()
    {
        $cityzens = $this->runJob(3, 10);

        Mail::assertSent(GeneralMailable::class, function ($mail) use ($cityzens) {
            $this->assertEquals($cityzens[0]->email, $mail->to[0]['address']);

            return true;
        });
    }

    public function testHandleSendBecauseCount()
    {
        $cityzen = $this->runJob(10, 2)[0];

        Mail::assertSent(GeneralMailable::class, function ($mail) use ($cityzen) {
            $this->assertEquals($cityzen->email, $mail->to[0]['address']);

            return true;
        });
    }

    public function testLimit()
    {
        Carbon::setTestNow();
        config(['app.send_hour_limit' => 2]);
        $cityzens = $this->runJob(1, 1, 5);
        $this->assertDatabaseHas('cityzen_boards', [
            'cityzen_id' => $cityzens[0]->id,
            'date_sent' => dateTimeNow()
        ]);
        $this->assertDatabaseHas('cityzen_boards', [
            'cityzen_id' => $cityzens[2]->id,
            'date_sent' => null
        ]);
        $job = new SendEmails();
        $job->handle();
        $this->assertDatabaseHas('cityzen_boards', [
            'cityzen_id' => $cityzens[3]->id,
            'date_sent' => dateTimeNow()
        ]);
        $this->assertDatabaseHas('cityzen_boards', [
            'cityzen_id' => $cityzens[4]->id,
            'date_sent' => null
        ]);
    }

    private function runJob($maxDays, $minCount, $cityzensCount = 1)
    {
        Mail::fake();
        DB::update("update cityzens set receive_type = ''");

        $board  = Board::factory()->create(['ready_at' => dateTimeNow('-3 days')]);
        $board2 = Board::factory()->create(['ready_at' => dateTimeNow('-5 days')]);
        $board3 = Board::factory()->create(['ready_at' => dateTimeNow('-1 days')]);
        Attachment::factory()->create(['board_id' => $board->id]);
        Attachment::factory()->create(['board_id' => $board2->id]);
        Attachment::factory()->create(['board_id' => $board3->id]);

        $cityzens = [];

        for ($i = 0; $i < $cityzensCount; $i++) {
            $cityzen = Cityzen::factory()->create([
                'min_emails' => $minCount,
                'day_emails' => $maxDays,
            ]);
            CityzenBoard::create([
                'cityzen_id' => $cityzen->id,
                'board_id' => $board->id,
            ]);
            CityzenBoard::create([
                'cityzen_id' => $cityzen->id,
                'board_id' => $board2->id,
            ]);
            CityzenBoard::create([
                'cityzen_id' => $cityzen->id,
                'board_id' => $board3->id,
            ]);
            $cityzens[] = $cityzen;
        }

        $job = new SendEmails();
        $job->handle();

        return $cityzens;
    }
}
