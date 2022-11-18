<?php

namespace Wooturk;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class CategoryServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Route::get('/user', [UserController::class, 'index']);
		Route::post('/user', [UserController::class, 'post']);
		Route::group(['middleware' => ['auth:sanctum']], function(){
			Route::get('/users', [UserController::class, 'list']);
			Route::get('/user/{id}', [UserController::class, 'get']);
			Route::put('/user/{id}', [UserController::class, 'put']);
			Route::delete('/user/{id}', [UserController::class, 'delete']);
		});
	}
}
