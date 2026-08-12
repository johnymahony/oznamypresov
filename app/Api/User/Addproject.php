<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 5. 10. 2019
 * Time: 9:10
 */

namespace App\Api\User;


use App\Project;

class Addproject
{
    public function handle($params)
    {
        return Project::create($params);
    }
}