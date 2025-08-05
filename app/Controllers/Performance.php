<?php

namespace App\Controllers;

use App\Libraries\PerformanceMonitor;
use App\Libraries\EmailQueueLibrary;

class Performance extends BaseController
{
    private $performanceMonitor;
    private $emailQueue;

    public function __construct()
    {
        $this->performanceMonitor = new PerformanceMonitor();
        $this->emailQueue = new EmailQueueLibrary();
    }

    /**
     * Display performance dashboard
     */
    public function index()
    {
        // Start monitoring this request
        $this->performanceMonitor->start();

        $data = [
            'title' => 'Performance Dashboard',
            'stats' => $this->performanceMonitor->getStats(7),
            'slowest_operations' => $this->performanceMonitor->getSlowestOperations(10),
            'system_resources' => $this->performanceMonitor->getSystemResources(),
            'email_queue_stats' => $this->emailQueue->getQueueStats(),
        ];

        // End monitoring
        $metrics = $this->performanceMonitor->end('performance_dashboard');

        return view('admin/performance/dashboard', $data);
    }

    /**
     * API endpoint to receive performance data from client
     */
    public function api()
    {
        // Only accept POST requests
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405);
        }

        $data = $this->request->getJSON(true);
        
        if (!$data) {
            return $this->response->setStatusCode(400);
        }

        // Store performance data
        $this->storeClientPerformanceData($data);

        return $this->response->setJSON(['status' => 'success']);
    }

    /**
     * Process email queue
     */
    public function processEmailQueue()
    {
        $processed = $this->emailQueue->processQueue(20);
        
        return $this->response->setJSON([
            'status' => 'success',
            'processed' => $processed,
            'message' => "Processed {$processed} emails"
        ]);
    }

    /**
     * Clean old performance data
     */
    public function cleanOldData()
    {
        $daysOld = $this->request->getPost('days') ?? 30;
        
        $metricsDeleted = $this->performanceMonitor->cleanOldMetrics($daysOld);
        $emailsDeleted = $this->emailQueue->cleanOldEmails($daysOld);
        
        return $this->response->setJSON([
            'status' => 'success',
            'metrics_deleted' => $metricsDeleted,
            'emails_deleted' => $emailsDeleted,
            'message' => "Cleaned old data: {$metricsDeleted} metrics, {$emailsDeleted} emails"
        ]);
    }

    /**
     * Get performance statistics as JSON
     */
    public function stats()
    {
        $days = $this->request->getGet('days') ?? 7;
        
        $data = [
            'stats' => $this->performanceMonitor->getStats($days),
            'system_resources' => $this->performanceMonitor->getSystemResources(),
            'email_queue_stats' => $this->emailQueue->getQueueStats(),
        ];

        return $this->response->setJSON($data);
    }

    /**
     * Store client-side performance data
     */
    private function storeClientPerformanceData($data)
    {
        try {
            $db = \Config\Database::connect();
            
            $performanceData = [
                'operation' => 'client_performance',
                'execution_time' => $data['performance']['measures']['total-load-time'] ?? 0,
                'memory_usage' => 0, // Client doesn't provide memory info
                'peak_memory' => 0,
                'url' => $data['url'] ?? '',
                'user_agent' => $data['userAgent'] ?? '',
                'ip_address' => $this->request->getIPAddress(),
                'timestamp' => new \CodeIgniter\I18n\Time('now'),
                'client_data' => json_encode($data['performance'] ?? []),
            ];

            $db->table('performance_metrics')->insert($performanceData);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to store client performance data: ' . $e->getMessage());
        }
    }

    /**
     * Export performance data
     */
    public function export()
    {
        $format = $this->request->getGet('format') ?? 'json';
        $days = $this->request->getGet('days') ?? 30;
        
        $stats = $this->performanceMonitor->getStats($days);
        $slowest = $this->performanceMonitor->getSlowestOperations(50);
        
        $data = [
            'stats' => $stats,
            'slowest_operations' => $slowest,
            'export_date' => date('Y-m-d H:i:s'),
            'period_days' => $days,
        ];

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        return $this->response->setJSON($data);
    }

    /**
     * Export data to CSV
     */
    private function exportToCsv($data)
    {
        $filename = 'performance_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($output, ['Operation', 'Avg Execution Time (ms)', 'Max Execution Time (ms)', 'Total Operations']);
        
        // Write data
        foreach ($data['stats'] as $stat) {
            fputcsv($output, [
                $stat['operation'],
                round($stat['avg_execution_time'], 2),
                round($stat['max_execution_time'], 2),
                $stat['total_operations']
            ]);
        }
        
        fclose($output);
        return $this->response;
    }
}