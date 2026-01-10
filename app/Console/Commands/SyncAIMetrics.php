<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AIMetricsService;

class SyncAIMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:sync-metrics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ dữ liệu từ AIConsultation sang AISession với accuracy_score và user_satisfaction';

    /**
     * Execute the console command.
     */
    public function handle(AIMetricsService $metricsService)
    {
        $this->info('Đang đồng bộ dữ liệu AI metrics...');
        
        $count = $metricsService->syncConsultationsToSessions();
        
        $this->info("Đã tạo {$count} AISession mới từ các consultations hiện có.");
        
        return Command::SUCCESS;
    }
}

