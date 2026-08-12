<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class DevController extends Controller
{
    public function up() {
        $r = \Illuminate\Support\Facades\Artisan::call('up');
        var_dump($r);
    }


    public function deploy()
    {
        \App\Log::debug("Deployment start");
        $commands = [
            'down',
            'migrate',
            'cache:clear',
            'view:clear',
            'config:cache',
            'up'
        ];

        foreach ($commands as $command) {
            $r = \Illuminate\Support\Facades\Artisan::call($command);
            echo "$command :<br>";
            var_dump($r);
            echo '<br>';
        }
//    try {
//        echo "Daemon: ";
//        var_dump(shell_exec("daemon 1 restart"));
//    } catch (\Exception $e) {
//        echo "Daemon error: " . $e->getMessage();
//    }

        \App\Log::debug("Deployment end");
    }
    public function artisan($script)
    {
        echo "<pre>";
        $r = \Illuminate\Support\Facades\Artisan::call($script);
        print_r($r);
        echo "</pre>";
    }

    public function cadasters()
    {
        $file = 'cadasters.json';
        if (Storage::exists($file)) {
            $data = json_decode(Storage::get($file), 1);
        } else {
            $data = [];
        }
        dd(\App\CadasterNumber::all()->pluck('city'), $data);
    }
}
