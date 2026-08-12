<?php

namespace Tests\Api\Project;

use App\Api\User\Getprojects;
use App\Project;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GetprojectsTest extends TestCase
{
    use DatabaseTransactions;

    public function testHandle()
    {
        /** @var Project $project */
        $user     = User::factory()->create();
        $user2    = User::factory()->create();
        $project  = Project::factory()->create(['user_id' => $user->id]);
        $project2 = Project::factory()->create(['user_id' => $user->id]);
        $project3 = Project::factory()->create(['user_id' => $user2->id]);

        $projects = (new Getprojects())->handle(['user_id' => $project->user_id]);
        $this->assertEquals(2, $projects->count());
    }
}
