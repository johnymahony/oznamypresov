<?php

namespace Tests\Process;

use App\Board;
use App\Geo\Geocoder;
use App\Process\BoardProcessor;
use App\Process\StreetInTitle;
use App\StreetName;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StreetInTitleTest extends TestCase
{
    use DatabaseTransactions;

    public function testProcess()
    {
        config(['app.city' => 'Banskabystrica']);
        StreetName::create([
            'name' => 'Dobšinského'
        ]);

        /** @var Board $board */
        $board = Board::factory()->create([
            'category_id' => 3,
            'description' => 'Prerušenie dodávky e.energie 13.05.2021- Dobšinského, Hečkova, Ondrejova, Na Karlove, Jurovského'
        ]);

        $geocoderMock = $this->getMockBuilder(Geocoder::class)->onlyMethods(['addressToPoint'])->getMock();
        $geocoderMock->method('addressToPoint')->willReturn(true);

        $electricity = new StreetInTitle($geocoderMock);

        (new BoardProcessor([$electricity]))->process($board);
        $this->assertEquals(Board::STATUS_DONE, $board->status);
        config(['app.city' => 'Presov']);
    }
}
