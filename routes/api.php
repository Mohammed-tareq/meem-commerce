<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Marvel\Enums\Permission;
// use Marvel\Enums\Role;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->get('/test', function (Request $request) {
  $role = \Spatie\Permission\Models\Role::where("name" , "super_admin")->first();
  $role->syncPermissions(["view-flash-sale",'create-flash-sale','update-flash-sale' ,'delete-flash-sale']);
return $role;
});

