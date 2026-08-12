<?php

namespace App\Providers;

use App\Board;
use App\BoardPoint;
use App\Category;
use App\Cities\Banskabystrica\Banskabystrica;
use App\Cities\Hlohovec\Hlohovec;
use App\Cities\Poprad\Poprad;
use App\Cities\Presov\Presov;
use App\Cities\Trencin\Trencin;
use App\Helpers\CadasterNumber;
use App\Log;
use App\Observers\BoardObserver;
use App\Observers\BoardPointObserver;
use App\Observers\CategoryObserver;
use App\Observers\ParcelObserver;
use App\Parcel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Category::observe(CategoryObserver::class);
        Parcel::observe(ParcelObserver::class);
        BoardPoint::observe(BoardPointObserver::class);
        Board::observe(BoardObserver::class);

        Blade::if('prod', function () {
            return config('app.env') === 'PROD';
        });

        DB::listen(function ($sql) {
            if (Log::$disableQueryLog) {
                return;
            }
            // $sql is an object with the properties:
            // sql: The query
            // bindings: the sql query variables
            // time: The execution time for the query
            // connectionName: The name of the connection

            // To save the executed queries to file:
            // Process the sql and the bindings:
            foreach ($sql->bindings as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } else {
                    if (is_string($binding)) {
                        $sql->bindings[$i] = "'$binding'";
                    }
                }
            }

            // Insert bindings into query
            $querys = str_replace(array(
                '%',
                '?'
            ), array(
                '%%',
                '%s'
            ), $sql->sql);
            if ( ! empty($sql->bindings)) {
                $querys = vsprintf($querys, $sql->bindings);
            }

            $dontLogPatterns = [
                '/^insert into `jobs/i',
                '/^update `jobs` set `reserved/i',
                '/^SELECT \* FROM `jobs` WHERE `queue`/i',
            ];
            foreach ($dontLogPatterns as $pattern) {
                if (preg_match($pattern, $querys)) {
                    return;
                }
            }

            // Save the query to file
            Log::debug("Query:|" . $sql->time . "|" . $querys);
//            if(preg_match("/^insert/i", $querys)) {
//                Log::debug("InsertId: ".DB::getPdo()->lastInsertId());
//            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CadasterNumber::class, function ($app) {
            return new CadasterNumber();
        });

        foreach (cities() as $cityClass) {
            $this->app->singleton($cityClass, function ($app) use ($cityClass) {
                return new $cityClass();
            });
        }
    }
}
