<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;

#Admin Routes
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Admin\CategoriesController;

Auth::routes();

Route::group(['middleware'=>'auth'],function(){
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('/people',[HomeController::class,'search'])->name('search');

    // Open the create post page(create.blade.php)
    Route::get('/post/create',[PostController::class,'create'])->name('post.create');
    Route::post('/post/store',[PostController::class,'store'])->name('post.store');
    Route::get('/post/{id}/show',[PostController::class,'show'])->name('post.show');
    Route::get('/post/{id}/edit',[PostController::class,'edit'])->name('post.edit');
    Route::patch('/post/{id}/update',[PostController::class,'update'])->name('post.update');
    Route::delete('/post/{id}/destroy',[PostController::class,'destroy'])->name('post.destroy');

    //Routes related for comments
    Route::post('/comment/{post_id}/store',[CommentController::class,'store'])->name('comment.store');
    Route::delete('/comment/{id}/destroy',[CommentController::class,'destroy'])->name('comment.destroy');

    //Routes related for User Profile
    Route::get('/profile/{id}/show',[ProfileController::class,'show'])->name('profile.show');
    Route::get('/profile/edit',[ProfileController::class,'edit'])->name('profile.edit');
    Route::patch('profile/update',[ProfileController::class,'update'])->name('profile.update');
    Route::get('/profile/{id}/followers',[ProfileController::class,'followers'])->name('profile.followers');
    Route::get('/profile/{id}/following',[ProfileController::class,'following'])->name('profile.following');

    //Route related to likes
    Route::post('/like/{post_id}/store',[LikeController::class,'store'])->name('like.store');
    Route::delete('/like/{post_id}/destroy',[LikeController::class,'destroy'])->name('like.destroy');

    // Routes related to Follows/Following
    Route::post('/follow/{user_id}/store',[FollowController::class,'store'])->name('follow.store');
    Route::delete('/follow/{user_id}/destroy',[FollowController::class,'destroy'])->name('follow.destroy');

    //Routes related to Admin Users
    Route::group(['prefix'=>'admin','as'=>'admin.','middleware'=>'admin'],function(){
        //Users
        Route::get('/users',[UsersController::class,'index'])->name('users'); //admin.users
        Route::delete('/users/{id}/deactivate',[UsersController::class,'deactivate'])->name('users.deactivate'); //admin.users.deactivate
        Route::patch('/users/{id}/activate',[UsersController::class,'activate'])->name('users.activate');

        //Posts
        Route::get('/posts',[PostsController::class,'index'])->name('posts'); //admin.posts
        Route::delete('/posts/{id}/hide',[PostsController::class,'hide'])->name('posts.hide');
        Route::patch('posts/{id}/unhide',[PostsController::class,'unhide'])->name('posts.unhide');

        //Categories
        Route::get('categories',[CategoriesController::class,'index'])->name('categories'); //admin.categories
        Route::post('/categories/store',[CategoriesController::class,'store'])->name('categories.store'); //admin.categories.store
        Route::patch('/categories/{id}/update',[CategoriesController::class,'update'])->name('categories.update');
        Route::delete('/categories/{id}/destroy',[CategoriesController::class,'destroy'])->name('categories.destroy');
    });
});