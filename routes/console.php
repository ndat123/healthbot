<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule để gửi reminders hàng ngày
Schedule::command('reminders:send')
    ->daily()
    ->at('08:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->description('Gửi nhắc nhở sức khỏe và cuộc hẹn cho users');

// Schedule để gửi appointment reminders (mỗi phút)
Schedule::command('appointments:send-reminders')
    ->everyMinute()
    ->timezone('Asia/Ho_Chi_Minh')
    ->description('Gửi thông báo nhắc nhở cho appointments sắp đến (trước 5 phút)');
