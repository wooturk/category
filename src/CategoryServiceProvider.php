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
		Route::get('/category', [CategoryController::class, 'index'])->name('category-index');
		Route::get('/categories', [CategoryController::class, 'list'])->name('category-list');
		Route::get('/category/{id}', [CategoryController::class, 'get'])->name('category-get');
		Route::group(['middleware' => ['auth:sanctum','wooturk.gateway']], function(){
			Route::post('/category', [CategoryController::class, 'post'])->name('category-create');
			Route::put('/category/{id}', [CategoryController::class, 'put'])->name('category-update');
			Route::delete('/category/{id}', [CategoryController::class, 'delete'])->name('category-delete');
		});
	}
}
