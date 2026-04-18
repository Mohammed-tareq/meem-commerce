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
  dd(auth()->user()->hasPermissionTo('create-faq'));
  $role = \Spatie\Permission\Models\Role::where("name" , "super_admin")->first();

  $role->syncPermissions(["view-faqs",'create-faqs','update-faqs' ,'delete-faqs']);
return $role;
});

