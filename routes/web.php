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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

use Illuminate\Http\Request;

// First route that user visits on consumer app
Route::get('/redirect', function () {
    // Build the query parameter string to pass auth information to our request
    $query = http_build_query([
        'client_id' => 1,
        'redirect_uri' => 'http://localhost:2000/callback',
        'response_type' => 'code',
        'scope' => '*'
    ]);
    // Redirect the user to the OAuth authorization page
    return redirect('http://localhost:2000/oauth/authorize?' . $query);
});

// Route that user is forwarded back to after approving on server
Route::get('/callback', function (Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post('http://localhost:2000/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => 1, // from admin panel above
            'client_secret' => 'Ys0ftKDZlAAB8riybyApoVhmpkMpYXEtd1FoVtNI', // from admin panel above
            'redirect_uri' => 'http://localhost:2000/callback',
            'code' => $request->code // Get code from the callback
        ]
    ]);

    // echo the access token; normally we would save this in the DB
    return json_decode((string) $response->getBody(), true)['access_token'];
});