<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\AIConsultation;
use App\Models\AISession;
use App\Models\HealthProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate User Activity Report
     */
    public function generateUserActivityReport(string $format = 'csv'): ?Report
    {
        try {
            $report = Report::create([
                'name' => 'Monthly User Activity',
                'category' => 'User Analytics',
                'type' => $format,
                'status' => 'pending',
            ]);

            // Collect data
            $users = User::with('settings')->get();
            $data = [];
            
            foreach ($users as $user) {
                $data[] = [
                    'ID' => $user->id,
                    'Name' => $user->name,
                    'Email' => $user->email,
                    'Role' => $user->role,
                    'Status' => $user->status,
                    'Created At' => $user->created_at->format('Y-m-d H:i:s'),
                    'Last Login' => $user->last_login ? $user->last_login->format('Y-m-d H:i:s') : 'Never',
                    'Phone' => $user->phone ?? 'N/A',
                ];
            }

            $filePath = $this->generateFile($report, $data, 'user_activity');
            
            if ($filePath) {
                $report->markAsReady($filePath);
                return $report;
            }

            $report->markAsFailed();
            return null;
        } catch (\Exception $e) {
            Log::error('Error generating user activity report: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate AI Performance Report
     */
    public function generateAIPerformanceReport(string $format = 'csv'): ?Report
    {
        try {
            $report = Report::create([
                'name' => 'AI Performance Report',
                'category' => 'AI Analytics',
                'type' => $format,
                'status' => 'pending',
            ]);

            // Collect data
            $consultations = AIConsultation::with('user')->get();
            $data = [];
            
            foreach ($consultations as $consultation) {
                $data[] = [
                    'ID' => $consultation->id,
                    'User ID' => $consultation->user_id,
                    'User Name' => $consultation->user->name ?? 'N/A',
                    'Session ID' => $consultation->session_id,
                    'Message Count' => $consultation->message_count ?? 0,
                    'Duration (seconds)' => $consultation->duration_seconds ?? 0,
                    'Created At' => $consultation->created_at->format('Y-m-d H:i:s'),
                    'Emergency Level' => $consultation->emergency_level ?? 'normal',
                ];
            }

            $filePath = $this->generateFile($report, $data, 'ai_performance');
            
            if ($filePath) {
                $report->markAsReady($filePath);
                return $report;
            }

            $report->markAsFailed();
            return null;
        } catch (\Exception $e) {
            Log::error('Error generating AI performance report: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate Health Trend Analysis Report
     */
    public function generateHealthTrendReport(string $format = 'csv'): ?Report
    {
        try {
            $report = Report::create([
                'name' => 'Health Trend Analysis',
                'category' => 'Health Analytics',
                'type' => $format,
                'status' => 'pending',
            ]);

            // Collect health data
            $users = User::with('settings')->get();
            $data = [];
            
            foreach ($users as $user) {
                $healthProfile = HealthProfile::where('user_id', $user->id)->latest()->first();
                $bloodPressure = 'N/A';
                if ($healthProfile && $healthProfile->blood_pressure_systolic && $healthProfile->blood_pressure_diastolic) {
                    $bloodPressure = $healthProfile->blood_pressure_systolic . '/' . $healthProfile->blood_pressure_diastolic;
                }
                
                $data[] = [
                    'User ID' => $user->id,
                    'User Name' => $user->name,
                    'Age' => $user->date_of_birth ? Carbon::parse($user->date_of_birth)->age : 'N/A',
                    'Gender' => $user->gender ?? 'N/A',
                    'BMI' => $healthProfile->bmi ?? 'N/A',
                    'Blood Pressure' => $bloodPressure,
                    'Last Updated' => $healthProfile ? $healthProfile->updated_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            }

            $filePath = $this->generateFile($report, $data, 'health_trend');
            
            if ($filePath) {
                $report->markAsReady($filePath);
                return $report;
            }

            $report->markAsFailed();
            return null;
        } catch (\Exception $e) {
            Log::error('Error generating health trend report: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate file based on format
     */
    protected function generateFile(Report $report, array $data, string $filename): ?string
    {
        try {
            $timestamp = now()->format('Y-m-d_His');
            $extension = $report->type;
            
            switch ($report->type) {
                case 'csv':
                    return $this->generateCSV($data, $filename, $timestamp);
                case 'excel':
                    return $this->generateExcel($data, $filename, $timestamp);
                case 'pdf':
                    return $this->generatePDF($data, $report, $filename, $timestamp);
                default:
                    return $this->generateCSV($data, $filename, $timestamp);
            }
        } catch (\Exception $e) {
            Log::error('Error generating file: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate CSV file
     */
    protected function generateCSV(array $data, string $filename, string $timestamp): string
    {
        $fileName = "reports/{$filename}_{$timestamp}.csv";
        $filePath = storage_path("app/public/{$fileName}");
        
        // Ensure directory exists
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = fopen($filePath, 'w');
        
        if (!empty($data)) {
            // Write headers
            fputcsv($file, array_keys($data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }
        
        fclose($file);

        return $fileName;
    }

    /**
     * Generate Excel file (placeholder - requires maatwebsite/excel package)
     */
    protected function generateExcel(array $data, string $filename, string $timestamp): string
    {
        // For now, fallback to CSV
        // TODO: Implement Excel generation using maatwebsite/excel
        return $this->generateCSV($data, $filename, $timestamp);
    }

    /**
     * Generate PDF file (placeholder - requires dompdf package)
     */
    protected function generatePDF(array $data, Report $report, string $filename, string $timestamp): string
    {
        // For now, fallback to CSV
        // TODO: Implement PDF generation using barryvdh/laravel-dompdf
        return $this->generateCSV($data, $filename, $timestamp);
    }

    /**
     * Download all reports as ZIP
     */
    public function downloadAllReports(): ?string
    {
        try {
            $reports = Report::ready()->get();
            
            if ($reports->isEmpty()) {
                return null;
            }

            $zipFileName = 'reports/all_reports_' . now()->format('Y-m-d_His') . '.zip';
            $zipFilePath = storage_path("app/public/{$zipFileName}");
            
            // Ensure directory exists
            $directory = dirname($zipFilePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Cannot create zip file');
            }

            foreach ($reports as $report) {
                if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                    $fileContent = Storage::disk('public')->get($report->file_path);
                    $zip->addFromString(basename($report->file_path), $fileContent);
                }
            }

            $zip->close();

            return $zipFileName;
        } catch (\Exception $e) {
            Log::error('Error creating zip file: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all available reports
     */
    public function getAvailableReports()
    {
        return Report::orderBy('generated_at', 'desc')->get();
    }
}

