<?php

namespace Tests\Console\Commands;

use App\Attachment;
use App\Board;
use App\Category;
use App\Console\Commands\Scrapper;
use App\Parcel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ScrapperTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testResolveParcels()
    {
        $scrapper = new Scrapper();


        $board = new Board();
        $board->id = 6465465;
        $board->category_id = Category::first()->id;
        $board->title = 'KNC 123';
        $board->description = 'KNC 456, KNC 999';
        $board->save();
        $attachment = new Attachment();
        $attachment->plain_text = 'KNC 999, katastrálne územie Nižná Šebastová';
        $attachment->board_id = $board->id;
        $attachment->save();
        $parcels = $scrapper->resolveParcels($attachment, $board);
        $this->assertArrayHasKey('KNC 999', $parcels);
        $this->assertArrayHasKey('KNC 123', $parcels);
        $this->assertArrayHasKey('KNC 456', $parcels);
        $board = $board->fresh();
        $this->assertEquals(cadasters()->search('Nižná Šebastová'), $board->cadaster_number);
    }

    public function testTitleForWholeCity()
    {
        $is = city()->isTitleForWholeCity('Poradovník žiadateľov o pridelenie nájomného bytu na ul. Sabinovskej a ul. Majakovského, na ul. Arm. gen. Svobodu, K Starej tehelni k 24.10.2018.');
        $this->assertTrue($is);
        $is = city()->isTitleForWholeCity('Poradovník žiadatel\'ov o pridelenie nájomného bytu na ulici K Starej tehelni , Sabinovskej, Majakovského, Antona Prídavka, Arm.gen.Svobodu k 14.11.2018');
        $this->assertTrue($is);
    }
}
