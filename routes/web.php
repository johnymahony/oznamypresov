<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use App\Board;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use PHPCoord\OSRef;

Auth::routes();

Route::any('/', 'IndexController@index');
Route::any('/robots.txt', function () {
    echo "User-agent: *
Allow: /
Sitemap: https://www." . city()->domain() . "/sitemap.xml";
});
Route::get('/board', function () {
    return Redirect::to('/', 301);
});
Route::post('/ajax/{method}', 'AjaxController@ajax');
Route::get('/download/{board}', 'IndexController@download');
Route::get('/mesta', 'CityController@listcities');
Route::get('/grafy', 'CityController@stats');
Route::get('/download/{board}/{type}', 'IndexController@download');
Route::post('/sss', 'IndexController@subscribe');
Route::post('/ocr', 'IndexController@ocr');
Route::view('/subscribe-success', 'subscribeSuccess');
Route::view('/update-success', 'updateSuccess');
Route::get('/objednavky', 'ObjednavkyController@objednavky');
Route::get('/objednavky/{ico}', 'ObjednavkyController@detail');
Route::get('/objednavky/{ico}/{year}', 'ObjednavkyController@detail');
Route::get('/objednavky/{ico}/{year}/{status}', 'ObjednavkyController@detail');
Route::get('/mapa', 'OverviewController@map');
Route::get('/projekty', 'ProjectsController@index');
Route::get('/projekt/{project}', 'ProjectsController@detail');
Route::get('/oznam/{board}/{slug}', 'OverviewController@board');
Route::get('/mapa/{rok}', function ($rok) {
    return redirect("/filter/$rok", 301);
});
Route::get('/mapa/{rok}/{kategoria}', function ($rok, $kategoria) {
    return redirect("/filter/$rok/$kategoria", 301);
});
Route::view('/contact', 'contact');
Route::view('/kontakt', 'contact');
Route::view('/zasady-ochrany-osobnych-udajov', 'policy');
Route::view('/no-address', 'no-address');
Route::get('/uradna-tabula', function () {
    return redirect('/', 301);
});
Route::get('/uradna-tabula/{rok}/{kategoria}', function ($rok, $kategoria) {
    return redirect("/filter/$rok/$kategoria", 301);
});
Route::get('/uradna-tabula/{rok}', function ($rok) {
    return redirect("/filter/$rok", 301);
});
Route::get('/filter', 'IndexController@index')->where('slashData', '(.*)');
Route::get('/filter/{slashData}', 'IndexController@index')->where('slashData', '(.*)');
Route::view('/oznamy', 'board');
Route::view('/ako-to-funguje', 'ako-to-funguje');
Route::view('/flash', 'flash');
Route::get('/logo/{blade}/{userId}/{time}', 'IndexController@logo');
Route::get('/verify/{hash}', 'IndexController@verify');
Route::get('/overenie/{hash}', 'IndexController@verify');
Route::get('/unsubscribe/{hash}', 'IndexController@unsubscribe');
Route::post('/savefeedback', 'IndexController@savefeedback');
Route::post('/save-no-address/{hash}', 'IndexController@savenoaddress');
Route::any('/cityzens', 'HomeController@cityzens')->middleware('auth');
Route::any('/edit/{board}', 'HomeController@edit')->middleware('auth');
Route::get('/delete-problem/{problem}', function (\App\Problem $problem) {
    $problem->delete();

    return redirect()->back();
})->middleware('auth');
Route::view('/edit/', 'all_done')->middleware('auth');
Route::view('/problems', 'problems')->middleware('auth');
Route::any('/run/{script}', 'DevController@artisan');
Route::get('/cadasters', 'DevController@cadasters');
Route::get('/stats', function () {
    echo \App\Board::whereStatus(Board::STATUS_READY)->count() . ' - obc: ' . \App\Cityzen::count() . ', probl: ' . \App\Problem::count();
});
Route::view('/work', 'work');
Route::get('/deploy', 'DevController@deploy');
Route::get('/up', 'DevController@up');

Route::group(['prefix' => 'reports', 'middleware' => 'auth'], function () {
    Route::get('/emails', 'ReportsController@emailsOpended');
    Route::get('/boards_sent', 'ReportsController@boardsSent');
});
Route::get('/sitemap.xml', function () {
    return response(file_get_contents(storage_path() . '/sitemap.xml'))
        ->header('Content-Type', 'text/xml');
});
Route::get('/tracelog/{modelName}/{modelId}', 'TraceLogController@show')->middleware('auth');