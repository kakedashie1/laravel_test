<?php

use App\Http\Controllers\Mypage\PostManageController;
use App\Http\Controllers\Mypage\UserLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SignUpController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PostController::class, 'index'] );
Route::get('posts/{post}', [PostController::class, 'show'])
        ->name('posts.show');

Route::get('signup', [SignUpController::class,'index']);
Route::post('signup', [SignUpController::class,'store']);

Route::get('mypage/login', [UserLoginController::class, 'index'])->name('login');
Route::post('mypage/login', [UserLoginController::class, 'login']);

Route::middleware('auth')->group(function () {
        Route::get('mypage/posts', [PostManageController::class, 'index'])->name('mypage.posts');
        Route::post('mypage/logout', [UserLoginController::class, 'logout'])->name('logout');
        Route::get('mypage/posts/create', [PostManageController::class, 'create']);
        Route::post('mypage/posts/create', [PostManageController::class, 'store'])->name('mypage.posts.store');
        Route::get('mypage/posts/edit/{post}', [PostManageController::class, 'edit'])->name('mypage.posts.edit');
        Route::post('mypage/posts/edit/{post}', [PostManageController::class, 'update']);
        Route::delete('mypage/posts/delete/{post}', [PostManageController::class, 'destroy'])->name('mypage.posts.delete');

});
