<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 5. 10. 2019
 * Time: 9:10
 */

namespace App\Api\User;


use App\Phase;
use App\Project;
use App\User;

class Getprojects
{
    public function handle($params)
    {
        return User::find($params['user_id'])->projects()->get()->each(function (Project $project) {
            $project->phases = Phase::all();
        });
    }
}