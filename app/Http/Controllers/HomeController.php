<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        // گرفتن پست‌ها با رابطه کاربر
        $posts = Post::with('user')->latest()->get();
        // پاس دادن پست‌ها به ویو
        return view('welcome', compact('posts'));
    }
}