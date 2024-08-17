<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
// use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
// use Inertia\Inertia;

Route::get('/', function() {
	return view('page');
});

Route::get('/getPosts', [PostController::class, 'getPosts']);
Route::get('/getFeeds', [PostController::class, 'getFeeds']);
Route::get('/getCategories', [PostController::class, 'getCategories']);
Route::get('/getLastUpdate', [PostController::class, 'getLastUpdate']);

Route::get('/igImage', [PostController::class, 'getInstagramImage']);
Route::get('/fbImage', [PostController::class, 'getFacebookImage']);

Route::middleware('auth')->group(function () {
	Route::get('/feeds', [FeedController::class, 'index'])->name('dashboard');
	Route::post('/feeds', [FeedController::class, 'store']);
	Route::post('/feeds/switchMultiple', [FeedController::class, 'switchStateMultiple']);
	Route::post('/feeds/deleteMultiple', [FeedController::class, 'destroyMultiple']);
	Route::post('/feeds/{feed}/switch', [FeedController::class, 'switchState']);
	Route::patch('/feeds/{feed}', [FeedController::class, 'update']);
	Route::delete('/feeds/{feed}', [FeedController::class, 'destroy']);

	Route::get('/posts', [PostController::class, 'index']);
	Route::delete('/posts/{post}', [PostController::class, 'destroy']);

	Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
	Route::post('/categories', [CategoryController::class, 'store']);
	Route::patch('/categories/{category}', [CategoryController::class, 'update']);
	Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

	Route::get('/test', [FeedController::class, 'test']);

	Route::get('/logs', [FeedController::class, 'logs']);

	// component examples
	Route::get('/components', fn() => inertia('Components/Inputs'));
	Route::get('/components/buttons', fn() => inertia('Components/Buttons'));
	Route::get('/components/tables', fn() => inertia('Components/Tables'));
	Route::get('/components/modals', fn() => inertia('Components/Modals'));
	Route::get('/components/cards', fn() => inertia('Components/Cards'));
	Route::get('/components/other', fn() => inertia('Components/Other'));

	// settings
	Route::get('/settings', [SettingController::class, 'index']);
	Route::post('/settings', [SettingController::class, 'update']);

	Route::middleware('isAdmin')->group(function () {
		// update feeds
		Route::get('/updateFeeds', [FeedController::class, 'processAllFeeds']);
		Route::get('/updateFeed/{feed}', [FeedController::class, 'processSingleFeed']);

		// users
		Route::get('/users', [UsersController::class, 'index'])->name('users');
		Route::post('/users', [UsersController::class, 'store']);
		Route::get('/users/{user}', [UsersController::class, 'edit'])->name('user.edit');
		Route::patch('/users/{user}', [UsersController::class, 'update']);
		Route::delete('/users/{user}', [UsersController::class, 'destroy']);

		// admin routes + cache functions
		Route::prefix('admin')->group(function () {
			Route::get('/', fn() => inertia('Admin'));

			// config cache
			Route::get('/configClear', function() { echo Artisan::call('config:clear'); });
			Route::get('/configCache', function() { echo Artisan::call('config:cache'); });

			// route cache
			Route::get('/routeClear', function() { echo Artisan::call('route:clear'); });
			Route::get('/routeCache', function() { echo Artisan::call('route:cache'); });

			// views cache
			Route::get('/viewClear', function() { echo Artisan::call('view:clear'); });
			Route::get('/viewCache', function() { echo Artisan::call('view:cache');	});

			// link storage
			// Route::get('/linkStorage', function() { echo Artisan::call('storage:link'); });
		});
	});
});

require __DIR__.'/auth.php';
