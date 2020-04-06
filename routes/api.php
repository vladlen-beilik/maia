<?php

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::namespace('\SpaceCode\Maia\Controllers')->group(function () {
    Route::post('/comments/postComments', 'ApiController@postComments');
    Route::post('/comments/getComments', 'ApiController@getComments');
});
