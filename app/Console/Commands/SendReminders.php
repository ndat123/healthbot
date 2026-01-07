<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReminderService;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi các nhắc nhở sức khỏe và cuộc hẹn cho users';

    protected $reminderService;

    /**
     * Create a new command instance.
     */
    public function __construct(ReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang gửi nhắc nhở...');

        $results = $this->reminderService->sendUpcomingReminders();

        $this->info("Đã gửi: {$results['sent']} nhắc nhở");
        $this->info("Thất bại: {$results['failed']} nhắc nhở");

        return Command::SUCCESS;
    }
}

