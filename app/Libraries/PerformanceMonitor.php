<?php

namespace App\Libraries;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * Performance Monitor Library
 * 
 * Tracks and analyzes application performance metrics:
 * - Request/response times
 * - Database query performance
 * - Memory usage
 * - Cache hit/miss ratios
 * - Core Web Vitals
 * - Custom performance markers
 */
class PerformanceMonitor
{
    protected $request;
    protected $response;
    protected $cache;
    protected $db;
    protected $session;
    
    protected $startTime;
    protected $startMemory;
    protected $markers = [];
    protected $queries = [];
    protected $cacheStats = [];
    protected $errors = [];
    
    // Performance thresholds
    protected $thresholds = [
        'response_time' => 2000,    // 2 seconds
        'db_query_time' => 100,     // 100ms per query
        'memory_usage' => 128,      // 128MB
        'cache_hit_ratio' => 0.8,   // 80% hit ratio
    ];
    
    public function __construct()
    {
        $this->request = Services::request();
        $this->cache = Services::cache();
        $this->db = \Config\Database::connect();
        $this->session = Services::session();
        
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
    }
    
    /**
     * Start monitoring a request
     */
    public function startRequest(): void
    {
        $this->mark('request_start');
        
        // Log request details
        $this->logRequestInfo();
        
        // Start database query monitoring
        $this->startDatabaseMonitoring();
        
        // Start cache monitoring
        $this->startCacheMonitoring();
    }
    
    /**
     * End monitoring and collect metrics
     */
    public function endRequest(ResponseInterface $response): void
    {
        $this->mark('request_end');
        $this->response = $response;
        
        // Calculate final metrics
        $metrics = $this->calculateMetrics();
        
        // Log performance data
        $this->logPerformanceMetrics($metrics);
        
        // Check for performance issues
        $this->analyzePerformance($metrics);
        
        // Store metrics for reporting
        $this->storeMetrics($metrics);
    }
    
    /**
     * Add a performance marker
     */
    public function mark(string $name, array $data = []): void
    {
        $this->markers[$name] = [
            'time' => microtime(true),
            'memory' => memory_get_usage(true),
            'data' => $data,
            'relative_time' => microtime(true) - $this->startTime,
            'memory_diff' => memory_get_usage(true) - $this->startMemory,
        ];
    }
    
    /**
     * Measure time between two markers
     */
    public function measure(string $startMarker, string $endMarker): array
    {
        if (!isset($this->markers[$startMarker]) || !isset($this->markers[$endMarker])) {
            return ['error' => 'Invalid markers'];
        }
        
        $start = $this->markers[$startMarker];
        $end = $this->markers[$endMarker];
        
        return [
            'duration' => ($end['time'] - $start['time']) * 1000, // milliseconds
            'memory_delta' => $end['memory'] - $start['memory'],
            'start_time' => $start['relative_time'],
            'end_time' => $end['relative_time'],
        ];
    }
    
    /**
     * Log database query performance
     */
    public function logQuery(string $sql, float $executionTime, array $bindings = []): void
    {
        $this->queries[] = [
            'sql' => $sql,
            'time' => $executionTime,
            'bindings' => $bindings,
            'memory_before' => memory_get_usage(true),
            'timestamp' => microtime(true) - $this->startTime,
            'slow' => $executionTime > ($this->thresholds['db_query_time'] / 1000),
        ];
    }
    
    /**
     * Log cache operations
     */
    public function logCacheOperation(string $operation, string $key, bool $hit = null, float $time = null): void
    {
        $this->cacheStats[] = [
            'operation' => $operation,
            'key' => $key,
            'hit' => $hit,
            'time' => $time,
            'timestamp' => microtime(true) - $this->startTime,
        ];
    }
    
    /**
     * Log performance errors or warnings
     */
    public function logPerformanceIssue(string $type, string $message, array $context = []): void
    {
        $this->errors[] = [
            'type' => $type,
            'message' => $message,
            'context' => $context,
            'timestamp' => microtime(true) - $this->startTime,
            'memory' => memory_get_usage(true),
        ];
    }
    
    /**
     * Get current performance snapshot
     */
    public function getSnapshot(): array
    {
        return [
            'current_time' => microtime(true) - $this->startTime,
            'current_memory' => memory_get_usage(true),
            'memory_delta' => memory_get_usage(true) - $this->startMemory,
            'peak_memory' => memory_get_peak_usage(true),
            'queries_count' => count($this->queries),
            'cache_operations' => count($this->cacheStats),
            'markers_count' => count($this->markers),
        ];
    }
    
    /**
     * Calculate comprehensive performance metrics
     */
    protected function calculateMetrics(): array
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        
        // Basic metrics
        $metrics = [
            'request_time' => ($endTime - $this->startTime) * 1000, // milliseconds
            'memory_usage' => $endMemory,
            'memory_delta' => $endMemory - $this->startMemory,
            'peak_memory' => $peakMemory,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        // Request info
        $metrics['request'] = [
            'method' => $this->request->getMethod(),
            'uri' => (string) $this->request->getUri(),
            'user_agent' => $this->request->getUserAgent()->__toString(),
            'ip' => $this->request->getIPAddress(),
            'is_ajax' => $this->request->isAJAX(),
        ];
        
        // Response info
        if ($this->response) {
            $metrics['response'] = [
                'status_code' => $this->response->getStatusCode(),
                'content_length' => strlen($this->response->getBody()),
                'headers_count' => count($this->response->getHeaders()),
            ];
        }
        
        // Database metrics
        $metrics['database'] = $this->calculateDatabaseMetrics();
        
        // Cache metrics
        $metrics['cache'] = $this->calculateCacheMetrics();
        
        // Markers analysis
        $metrics['markers'] = $this->analyzeMarkers();
        
        // Performance scores
        $metrics['scores'] = $this->calculatePerformanceScores($metrics);
        
        return $metrics;
    }
    
    /**
     * Calculate database performance metrics
     */
    protected function calculateDatabaseMetrics(): array
    {
        if (empty($this->queries)) {
            return ['total_queries' => 0];
        }
        
        $totalTime = array_sum(array_column($this->queries, 'time'));
        $slowQueries = array_filter($this->queries, fn($q) => $q['slow']);
        
        return [
            'total_queries' => count($this->queries),
            'total_time' => $totalTime * 1000, // milliseconds
            'average_time' => ($totalTime / count($this->queries)) * 1000,
            'slow_queries' => count($slowQueries),
            'longest_query' => max(array_column($this->queries, 'time')) * 1000,
            'queries' => $this->queries,
        ];
    }
    
    /**
     * Calculate cache performance metrics
     */
    protected function calculateCacheMetrics(): array
    {
        if (empty($this->cacheStats)) {
            return ['operations' => 0];
        }
        
        $hits = array_filter($this->cacheStats, fn($op) => $op['hit'] === true);
        $misses = array_filter($this->cacheStats, fn($op) => $op['hit'] === false);
        
        return [
            'operations' => count($this->cacheStats),
            'hits' => count($hits),
            'misses' => count($misses),
            'hit_ratio' => count($this->cacheStats) > 0 ? count($hits) / count($this->cacheStats) : 0,
            'operations_detail' => $this->cacheStats,
        ];
    }
    
    /**
     * Analyze performance markers
     */
    protected function analyzeMarkers(): array
    {
        $analysis = [];
        
        // Calculate durations between common markers
        $commonPairs = [
            ['request_start', 'controller_loaded'],
            ['controller_loaded', 'database_connected'],
            ['database_connected', 'view_loaded'],
            ['view_loaded', 'request_end'],
        ];
        
        foreach ($commonPairs as [$start, $end]) {
            if (isset($this->markers[$start]) && isset($this->markers[$end])) {
                $analysis[$start . '_to_' . $end] = $this->measure($start, $end);
            }
        }
        
        return [
            'total_markers' => count($this->markers),
            'durations' => $analysis,
            'markers' => $this->markers,
        ];
    }
    
    /**
     * Calculate performance scores (0-100)
     */
    protected function calculatePerformanceScores(array $metrics): array
    {
        $scores = [];
        
        // Response time score (0-100)
        $responseTime = $metrics['request_time'];
        $scores['response_time'] = max(0, 100 - ($responseTime / $this->thresholds['response_time'] * 100));
        
        // Memory usage score
        $memoryMB = $metrics['memory_delta'] / (1024 * 1024);
        $scores['memory'] = max(0, 100 - ($memoryMB / $this->thresholds['memory_usage'] * 100));
        
        // Database performance score
        if (isset($metrics['database']['total_queries']) && $metrics['database']['total_queries'] > 0) {
            $avgQueryTime = $metrics['database']['average_time'];
            $scores['database'] = max(0, 100 - ($avgQueryTime / $this->thresholds['db_query_time'] * 100));
        } else {
            $scores['database'] = 100;
        }
        
        // Cache performance score
        if (isset($metrics['cache']['operations']) && $metrics['cache']['operations'] > 0) {
            $hitRatio = $metrics['cache']['hit_ratio'];
            $scores['cache'] = $hitRatio * 100;
        } else {
            $scores['cache'] = 100;
        }
        
        // Overall score (weighted average)
        $weights = ['response_time' => 0.3, 'memory' => 0.2, 'database' => 0.3, 'cache' => 0.2];
        $scores['overall'] = array_sum(array_map(fn($score, $key) => $score * $weights[$key], $scores, array_keys($scores)));
        
        return $scores;
    }
    
    /**
     * Analyze performance and identify issues
     */
    protected function analyzePerformance(array $metrics): void
    {
        // Check response time
        if ($metrics['request_time'] > $this->thresholds['response_time']) {
            $this->logPerformanceIssue(
                'slow_response',
                "Slow response time: {$metrics['request_time']}ms",
                ['threshold' => $this->thresholds['response_time']]
            );
        }
        
        // Check memory usage
        $memoryMB = $metrics['memory_delta'] / (1024 * 1024);
        if ($memoryMB > $this->thresholds['memory_usage']) {
            $this->logPerformanceIssue(
                'high_memory',
                "High memory usage: {$memoryMB}MB",
                ['threshold' => $this->thresholds['memory_usage']]
            );
        }
        
        // Check database performance
        if (isset($metrics['database']['slow_queries']) && $metrics['database']['slow_queries'] > 0) {
            $this->logPerformanceIssue(
                'slow_queries',
                "Found {$metrics['database']['slow_queries']} slow database queries",
                ['threshold' => $this->thresholds['db_query_time']]
            );
        }
        
        // Check cache performance
        if (isset($metrics['cache']['hit_ratio']) && $metrics['cache']['hit_ratio'] < $this->thresholds['cache_hit_ratio']) {
            $this->logPerformanceIssue(
                'low_cache_hit_ratio',
                "Low cache hit ratio: {$metrics['cache']['hit_ratio']}",
                ['threshold' => $this->thresholds['cache_hit_ratio']]
            );
        }
    }
    
    /**
     * Log request information
     */
    protected function logRequestInfo(): void
    {
        if (ENVIRONMENT === 'development') {
            log_message('info', 'Performance Monitor: Request started - ' . $this->request->getMethod() . ' ' . $this->request->getUri());
        }
    }
    
    /**
     * Log performance metrics
     */
    protected function logPerformanceMetrics(array $metrics): void
    {
        // Log performance issues
        foreach ($this->errors as $error) {
            log_message('warning', "Performance Issue [{$error['type']}]: {$error['message']}", $error['context']);
        }
        
        // Log summary in development
        if (ENVIRONMENT === 'development') {
            $summary = sprintf(
                'Performance Summary: %dms response, %dMB memory, %d queries, Score: %.1f/100',
                round($metrics['request_time']),
                round($metrics['memory_delta'] / (1024 * 1024), 2),
                $metrics['database']['total_queries'] ?? 0,
                $metrics['scores']['overall']
            );
            log_message('info', $summary);
        }
    }
    
    /**
     * Store metrics for reporting
     */
    protected function storeMetrics(array $metrics): void
    {
        // Store in cache for real-time monitoring
        $cacheKey = 'perf_metrics_' . date('Y-m-d-H');
        $existingMetrics = $this->cache->get($cacheKey) ?? [];
        $existingMetrics[] = [
            'timestamp' => time(),
            'response_time' => $metrics['request_time'],
            'memory_usage' => $metrics['memory_delta'],
            'queries' => $metrics['database']['total_queries'] ?? 0,
            'cache_hit_ratio' => $metrics['cache']['hit_ratio'] ?? 1,
            'overall_score' => $metrics['scores']['overall'],
            'uri' => $metrics['request']['uri'],
        ];
        
        // Keep only last 1000 entries
        if (count($existingMetrics) > 1000) {
            $existingMetrics = array_slice($existingMetrics, -1000);
        }
        
        $this->cache->save($cacheKey, $existingMetrics, 3600); // 1 hour
    }
    
    /**
     * Start database monitoring
     */
    protected function startDatabaseMonitoring(): void
    {
        // Hook into database events if available
        // This would require CodeIgniter database event hooks
    }
    
    /**
     * Start cache monitoring
     */
    protected function startCacheMonitoring(): void
    {
        // Hook into cache operations if available
        // This would require cache event hooks
    }
    
    /**
     * Get performance report
     */
    public function getPerformanceReport(int $hours = 24): array
    {
        $report = [
            'period' => $hours . ' hours',
            'metrics' => [],
            'summary' => [],
            'recommendations' => [],
        ];
        
        // Collect metrics from cache
        for ($i = 0; $i < $hours; $i++) {
            $hourKey = date('Y-m-d-H', strtotime("-{$i} hours"));
            $cacheKey = 'perf_metrics_' . $hourKey;
            $hourMetrics = $this->cache->get($cacheKey) ?? [];
            $report['metrics'] = array_merge($report['metrics'], $hourMetrics);
        }
        
        if (!empty($report['metrics'])) {
            // Calculate summary statistics
            $responseTimes = array_column($report['metrics'], 'response_time');
            $memoryUsages = array_column($report['metrics'], 'memory_usage');
            $overallScores = array_column($report['metrics'], 'overall_score');
            
            $report['summary'] = [
                'total_requests' => count($report['metrics']),
                'avg_response_time' => array_sum($responseTimes) / count($responseTimes),
                'max_response_time' => max($responseTimes),
                'avg_memory_usage' => array_sum($memoryUsages) / count($memoryUsages),
                'avg_score' => array_sum($overallScores) / count($overallScores),
                'slow_requests' => count(array_filter($responseTimes, fn($t) => $t > $this->thresholds['response_time'])),
            ];
            
            // Generate recommendations
            $report['recommendations'] = $this->generateRecommendations($report['summary']);
        }
        
        return $report;
    }
    
    /**
     * Generate performance recommendations
     */
    protected function generateRecommendations(array $summary): array
    {
        $recommendations = [];
        
        if ($summary['avg_response_time'] > $this->thresholds['response_time']) {
            $recommendations[] = 'Consider implementing response caching and optimizing database queries';
        }
        
        if ($summary['avg_memory_usage'] > $this->thresholds['memory_usage'] * 1024 * 1024) {
            $recommendations[] = 'Review memory usage patterns and implement object pooling';
        }
        
        if ($summary['avg_score'] < 70) {
            $recommendations[] = 'Overall performance is below optimal. Consider comprehensive optimization';
        }
        
        if ($summary['slow_requests'] > $summary['total_requests'] * 0.1) {
            $recommendations[] = 'More than 10% of requests are slow. Review application bottlenecks';
        }
        
        return $recommendations;
    }
}