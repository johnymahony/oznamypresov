<?php
/**
 * Created by PhpStorm.
 * User: ivani
 * Date: 5. 10. 2019
 * Time: 9:11
 */

namespace App\Api\Project;

use App\Project;

class Setphase
{
    public function handle($params)
    {
        Project::findOrFail($params['project_id'])->update(['phase_id' => $params['phase_id']]);
    }
}