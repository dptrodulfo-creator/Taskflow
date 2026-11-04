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
            $table->string('username')->nullable()->unique();
            $table->string('profile_photo_path')->nullable();
            $table->string('theme')->default('light');
            $table->string('locale', 10)->default('en');
            $table->string('timezone', 64)->default('UTC');
            $table->boolean('notify_upcoming_deadlines')->default(true);
            $table->boolean('notify_overdue_tasks')->default(true);
            $table->boolean('notify_task_completions')->default(true);
            $table->timestamp('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn([
                'username',
                'profile_photo_path',
                'theme',
                'locale',
                'timezone',
                'notify_upcoming_deadlines',
                'notify_overdue_tasks',
                'notify_task_completions',
                'last_login_at',
            ]);
        });
    }
};
