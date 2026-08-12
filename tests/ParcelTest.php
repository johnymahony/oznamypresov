<?php

namespace Tests;

use App\Board;
use App\Parcel;

class ParcelTest extends TestCase
{
    public function testFixNumber()
    {
        /** @var Board $board */
        $board = Board::factory()->create();
        $parcel = $board->parcels()->create([
            'number' => '123/4',
        ]);

        $this->assertEquals('C 123/4', $parcel->number);
        $parcel = $board->parcels()->create([
            'number' => 'E 1123/4',
        ]);

        $this->assertEquals('E 1123/4', $parcel->number);
    }
}
