<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // تغییر ستون email به nullable
            $table->string('email')->nullable()->change();
            // مطمئن شدن از وجود national_code
            if (!Schema::hasColumn('users', 'national_code')) {
                $table->string('national_code', 10)->unique()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // برگرداندن email به حالت غیر nullable (اگه بخوای برگردونی)
            $table->string('email')->nullable(false)->change();
            // حذف national_code اگه اضافه شده باشه
            if (Schema::hasColumn('users', 'national_code')) {
                $table->dropColumn('national_code');
            }
        });
    }
};