<?php

namespace Tests\Http\Requests;

use App\Http\Requests\SubscribeRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscribeRequestTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $request = new SubscribeRequest(['email' => 'ivan.ivanco@gmail.com']);
        $request->handle();
        $this->assertDatabaseHas('messages', [
            'recepient' => 'ivan.ivanco@gmail.com'
        ]);
    }
}
