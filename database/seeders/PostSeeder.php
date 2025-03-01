<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // فرض می‌کنیم یه کاربر با national_code موجوده
        $user = User::where('national_code', '4411015056')->first();

        if ($user) {
            Post::create([
                'user_id' => $user->id,
                'title' => 'پست آزمایشی اول',
                'body' => 'این یه پست آزمایشی برای تست سیستم هست.',
            ]);

            Post::create([
                'user_id' => $user->id,
                'title' => 'پست آزمایشی دوم',
                'body' => 'این پست دومه و فقط برای نمایش بیشتره.',
            ]);

            Post::create([
                'user_id' => $user->id,
                'title' => 'پست آزمایشی سوم',
                'body' => 'پست سوم با یه محتوای متفاوت.',
            ]);
        }
    }
}