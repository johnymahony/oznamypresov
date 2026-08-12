<?php

namespace Tests\Jobs;

use App\Board;
use App\Console\Commands\DailySender;
use App\Console\Commands\SendEmails;
use App\Http\Ajax\AddParcel;
use App\Http\Ajax\AddPoint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DailySenderTest extends TestCase
{
    use DatabaseTransactions;

    public function testHandle()
    {
        DB::delete("delete from board_points");
        DB::delete("delete from boards");
        $cityzen = \App\Cityzen::factory()->create([
            'latitude'   => '48.996409907911804',
            'longitude'  => '21.252450942993168',
            'first_time' => false,
        ]);
        $cityzen->tag('Antona Prídavka');
        $cityzen->categories()->sync([1]);
        $board = $this->createEmptyBoard();
        $board->tag('Antona Prídavka');

        $boardWitPoint               = $this->createBoardWithPoint();
        $boardWitPointOutsideCytizen = $this->createBoardWithPointOutsideCityzen();

        $boardWithParcel = $this->createBoardWithParcel();

        $boardWithParcelOutsideCityzen = $this->createBoardWithParcelOutsideCytizen();
        $boardForWholeCity             = $this->createBoardForWholeCity();

        $emptyBoard = $this->createEmptyBoard();

        $job = new DailySender($this->getSenderMock());
        $job->handle();

        $board = Board::find($board->id);
        $this->assertEquals(1, $board->sent_daily);

        $boardWitPoint = Board::find($boardWitPoint->id);
        $this->assertEquals(1, $boardWitPoint->sent_daily);

        $boardWithParcel = Board::find($boardWithParcel->id);
        $this->assertEquals(1, $boardWithParcel->sent_daily);

        $boardWithParcelOutsideCityzen = Board::find($boardWithParcelOutsideCityzen->id);
        $this->assertEquals(0, $boardWithParcelOutsideCityzen->sent_daily);

        $boardWitPointOutsideCytizen = Board::find($boardWitPointOutsideCytizen->id);
        $this->assertEquals(0, $boardWitPointOutsideCytizen->sent_daily);

        $boardForWholeCity = Board::find($boardForWholeCity->id);
        $this->assertEquals(1, $boardForWholeCity->sent_daily);

        $emptyBoard = Board::find($emptyBoard->id);
        $this->assertEquals(0, $emptyBoard->sent_daily);

        $this->assertDatabaseHas('cityzen_boards', [
            'cityzen_id' => $cityzen->id
        ]);
    }

    private function createBoardWithParcelOutsideCytizen(): Board
    {
        $board = Board::factory()->create([
            'category_id' => 1,
            'status'      => Board::STATUS_DONE,
            'ready_at'    => dateTimeNow(),
            'title'       => 'createBoardWithParcelOutsideCytizen',
        ]);

        (new AddParcel())->handle([
            'board_id' => $board->id,
            'coords'   => [
                ['lat' => 49.008262606436055, 'lng' => 21.205030027913306],
                ['lat' => 49.02025320210386, 'lng' => 21.205030027913306],
                ['lat' => 49.02025320210386, 'lng' => 21.214908319285158],
                ['lat' => 49.008262606436055, 'lng' => 21.214908319285158],
            ]
        ]);

        return $board;
    }

    private function createBoardWithParcel(): Board
    {
        $board = Board::factory()->create([
            'category_id' => 1,
            'status'      => Board::STATUS_DONE,
            'ready_at'    => dateTimeNow(),
            'title'       => 'createBoardWithParcel',
        ]);

        (new AddParcel())->handle([
            'board_id' => $board->id,
            'coords'   => [
                ['lat' => 48.98832818524669, 'lng' => 21.24873323774723],
                ['lat' => 49.00280114330751, 'lng' => 21.24873323774723],
                ['lat' => 49.00280114330751, 'lng' => 21.261875660181047],
                ['lat' => 48.98832818524669, 'lng' => 21.261875660181047],
            ]
        ]);

        return $board;
    }

    private function createBoardWithPoint(): Board
    {
        $board = Board::factory()->create([
            'category_id' => 1,
            'status'      => Board::STATUS_DONE,
            'ready_at'    => dateTimeNow(),
            'title'       => 'createBoardWithPoint',
        ]);

        (new AddPoint())->handle([
            'board_id' => $board->id,
            'lat'      => '48.99797259847831',
            'lng'      => '21.248009204864506',
        ]);

        return $board;
    }

    private function createBoardWithPointOutsideCityzen(): Board
    {
        $board = Board::factory()->create([
            'category_id' => 1,
            'status'      => Board::STATUS_DONE,
            'ready_at'    => dateTimeNow(),
            'title'       => 'createBoardWithPointOutsideCityzen',
        ]);

        (new AddPoint())->handle([
            'board_id' => $board->id,
            'lat'      => '49.030102469327026',
            'lng'      => '21.177227432091367',
        ]);

        return $board;
    }

    private function createBoardForWholeCity(): Board
    {
        return Board::factory()->create([
            'category_id' => 1,
            'status'      => Board::STATUS_DONE,
            'ready_at'    => dateTimeNow(),
            'whole_city'  => 1,
            'title'       => 'createBoardForWholeCity',
        ]);
    }

    private function createEmptyBoard(): Board
    {
        return Board::factory()->create([
            'category_id' => 1,
            'status'      => Board::STATUS_DONE,
            'ready_at'    => dateTimeNow(),
            'title'       => 'createEmptyBoard',
        ]);
    }

    private function getSenderMock()
    {
        return $this->getMockBuilder(SendEmails::class)->onlyMethods(['handle'])->getMock();
    }
}
