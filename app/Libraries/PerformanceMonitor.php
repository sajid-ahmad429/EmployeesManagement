<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use Config\Services;

/**
 * Performance Monitor Library
 * Tracks application performance metrics and provides optimization insights
 */
class PerformanceMonitor
{
    private $startTime;
    private $memoryStart;
    private $cache;
    private $db;

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->memoryStart = memory_get_usage();
        $this->cache = Services::cache();
        $this->db = \Config\Database::connect();
    }

    /**
     * Start performance monitoring
     */
    public function start()
    {
        $this->startTime = microtime(true);
        $this->memoryStart = memory_get_usage();
    }

    /**
     * End performance monitoring and log metrics
     * 
     * @param string $operation
     * @return array
     */
    public function end($operation = 'unknown')
    {
        $endTime = microtime(true);
        $memoryEnd = memory_get_usage();
        $peakMemory = memory_get_peak_usage();

        $metrics = [
            'operation' => $operation,
            'execution_time' => round(($endTime - $this->startTime) * 1000, 2), // milliseconds
            'memory_usage' => round(($memoryEnd - $this->memoryStart) / 1024 / 1024, 2), // MB
            'peak_memory' => round($peakMemory / 1024 / 1024, 2), // MB
            'timestamp' => new Time('now'),
            'url' => current_url(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ];

        // Log slow operations
        if ($metrics['execution_time'] > 1000) { // Log operations taking more than 1 second
            $this->logSlowOperation($metrics);
        }

        // Store metrics for analysis
        $this->storeMetrics($metrics);

        return $metrics;
    }

    /**
     * Log slow operations
     * 
     * @param array $metrics
     */
    private function logSlowOperation($metrics)
    {
        log_message('warning', "SLOW OPERATION: {$metrics['operation']} took {$metrics['execution_time']}ms using {$metrics['memory_usage']}MB memory");
    }

    /**
     * Store performance metrics
     * 
     * @param array $metrics
     */
    private function storeMetrics($metrics)
    {
        try {
            $this->db->table('performance_metrics')->insert($metrics);
        } catch (\Exception $e) {
            // Fallback to cache if database is not available
            $this->cache->save('perf_' . uniqid(), $metrics, 3600);
        }
    }

    /**
     * Get performance statistics
     * 
     * @param int $days
     * @return array
     */
    public function getStats($days = 7)
    {
        $startDate = new Time("-{$days} days");
        
        $stats = $this->db->table('performance_metrics')
            ->select('
                operation,
                AVG(execution_time) as avg_execution_time,
                MAX(execution_time) as max_execution_time,
                AVG(memory_usage) as avg_memory_usage,
                MAX(memory_usage) as max_memory_usage,
                COUNT(*) as total_operations
            ')
            ->where('timestamp >=', $startDate)
            ->groupBy('operation')
            ->orderBy('avg_execution_time', 'DESC')
            ->get()
            ->getResultArray();

        return $stats;
    }

    /**
     * Get slowest operations
     * 
     * @param int $limit
     * @return array
     */
    public function getSlowestOperations($limit = 10)
    {
        return $this->db->table('performance_metrics')
            ->select('operation, execution_time, memory_usage, timestamp, url')
            ->orderBy('execution_time', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Monitor database query performance
     * 
     * @param callable $callback
     * @return mixed
     */
    public function monitorQuery($callback)
    {
        $this->start();
        $result = $callback();
        $metrics = $this->end('database_query');
        
        // Log slow queries
        if ($metrics['execution_time'] > 500) { // Log queries taking more than 500ms
            log_message('warning', "SLOW QUERY: {$metrics['execution_time']}ms - " . $this->getLastQuery());
        }
        
        return $result;
    }

    /**
     * Get the last executed query
     * 
     * @return string
     */
    private function getLastQuery()
    {
        $db = \Config\Database::connect();
        return $db->getLastQuery() ?? 'Unknown query';
    }

    /**
     * Monitor cache performance
     * 
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function monitorCache($key, $callback)
    {
        $this->start();
        $result = $this->cache->get($key);
        
        if ($result === null) {
            $result = $callback();
            $this->cache->save($key, $result, 3600);
        }
        
        $metrics = $this->end('cache_operation');
        return $result;
    }

    /**
     * Get system resource usage
     * 
     * @return array
     */
    public function getSystemResources()
    {
        return [
            'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2),
            'peak_memory' => round(memory_get_peak_usage() / 1024 / 1024, 2),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'current_time' => microtime(true),
        ];
    }

    /**
     * Clean old performance metrics
     * 
     * @param int $daysOld
     * @return int
     */
    public function cleanOldMetrics($daysOld = 30)
    {
        $cutoffDate = new Time("-{$daysOld} days");
        
        return $this->db->table('performance_metrics')
            ->where('timestamp <', $cutoffDate)
            ->delete();
    }
}