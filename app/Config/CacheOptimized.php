<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Optimized Cache Configuration
 * 
 * Performance improvements:
 * - Multiple cache handlers for different data types
 * - Hierarchical caching strategy
 * - Cache warming and invalidation strategies
 * - Performance monitoring
 */
class CacheOptimized extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Primary Handler
     * --------------------------------------------------------------------------
     *
     * The name of the preferred handler that should be used. If for some reason
     * it is not available, the $backupHandler will be used in its place.
     */
    public string $handler = 'redis';

    /**
     * --------------------------------------------------------------------------
     * Backup Handler
     * --------------------------------------------------------------------------
     *
     * The name of the handler that will be used in case the first one is
     * unreachable. Often, 'file' is used here since the filesystem is
     * always available, though that's not always practical for the app.
     */
    public string $backupHandler = 'file';

    /**
     * --------------------------------------------------------------------------
     * Cache Directory Path
     * --------------------------------------------------------------------------
     *
     * The path to where cache files should be stored, if using a file-based
     * cache handler.
     */
    public string $storePath = WRITEPATH . 'cache/';

    /**
     * --------------------------------------------------------------------------
     * Key Prefix
     * --------------------------------------------------------------------------
     *
     * This string is added to all cache item names to help avoid collisions
     * if you run multiple applications with the same cache engine.
     */
    public string $prefix = 'ems_cache_';

    /**
     * --------------------------------------------------------------------------
     * Default TTL
     * --------------------------------------------------------------------------
     *
     * The default number of seconds that items should be cached for when not
     * explicitly specified.
     *
     * WARNING: This is not used by framework handlers where 60 seconds is
     * hard-coded, but may be useful to set expired times for content-based
     * cache handlers like Cache Tags.
     */
    public int $ttl = 3600; // 1 hour default

    /**
     * --------------------------------------------------------------------------
     * Reserved Characters
     * --------------------------------------------------------------------------
     *
     * A string of reserved characters that will not be allowed in keys or tags.
     * Strings that contain any of the characters will cause handlers to throw.
     * Default: {}()/\@:
     */
    public string $reservedCharacters = '{}()/\@:';

    /**
     * --------------------------------------------------------------------------
     * Cache Handlers
     * --------------------------------------------------------------------------
     *
     * Settings for each cache handler. These are the settings that will be
     * passed to each handler when it is instantiated.
     */
    
    /**
     * Redis settings for high-performance caching
     */
    public array $redis = [
        'host'        => '127.0.0.1',
        'password'    => null,
        'port'        => 6379,
        'timeout'     => 0,
        'database'    => 0,
        'serializer'  => 'php', // php, json, igbinary
        'prefix'      => 'ems_',
    ];

    /**
     * File-based cache settings
     */
    public array $file = [
        'storePath' => WRITEPATH . 'cache/',
        'mode'      => 0640,
    ];

    /**
     * Memcached settings
     */
    public array $memcached = [
        'host'   => '127.0.0.1',
        'port'   => 11211,
        'weight' => 1,
        'raw'    => false,
    ];

    /**
     * --------------------------------------------------------------------------
     * Performance-Optimized Cache Strategies
     * --------------------------------------------------------------------------
     */
    
    /**
     * Cache warming configuration
     */
    public array $warming = [
        'enabled' => true,
        'schedule' => [
            'employees' => '0 */2 * * *', // Every 2 hours
            'departments' => '0 0 * * *', // Daily
            'stats' => '*/15 * * * *',    // Every 15 minutes
        ],
    ];

    /**
     * Cache layers for hierarchical caching
     */
    public array $layers = [
        'l1' => [
            'handler' => 'array',
            'ttl' => 300,      // 5 minutes
            'max_items' => 1000,
        ],
        'l2' => [
            'handler' => 'redis',
            'ttl' => 3600,     // 1 hour
            'max_items' => 10000,
        ],
        'l3' => [
            'handler' => 'file',
            'ttl' => 86400,    // 24 hours
            'max_items' => 50000,
        ],
    ];

    /**
     * Cache groups and their TTL settings
     */
    public array $groups = [
        'employee' => [
            'ttl' => 3600,           // 1 hour
            'tags' => ['user', 'data'],
            'auto_refresh' => true,
        ],
        'department' => [
            'ttl' => 7200,           // 2 hours
            'tags' => ['org', 'data'],
            'auto_refresh' => true,
        ],
        'auth' => [
            'ttl' => 1800,           // 30 minutes
            'tags' => ['security'],
            'auto_refresh' => false,
        ],
        'session' => [
            'ttl' => 7200,           // 2 hours
            'tags' => ['user', 'session'],
            'auto_refresh' => true,
        ],
        'stats' => [
            'ttl' => 900,            // 15 minutes
            'tags' => ['analytics'],
            'auto_refresh' => true,
        ],
        'static' => [
            'ttl' => 86400,          // 24 hours
            'tags' => ['static'],
            'auto_refresh' => false,
        ],
    ];

    /**
     * Cache invalidation patterns
     */
    public array $invalidation = [
        'patterns' => [
            'employee_*' => ['employee_list', 'stats_*', 'department_*'],
            'department_*' => ['department_list', 'employee_*', 'stats_*'],
            'auth_*' => ['session_*'],
        ],
        'events' => [
            'employee.created' => ['employee_*', 'stats_*'],
            'employee.updated' => ['employee_*', 'stats_*'],
            'employee.deleted' => ['employee_*', 'stats_*'],
            'department.created' => ['department_*', 'employee_*'],
            'department.updated' => ['department_*', 'employee_*'],
        ],
    ];

    /**
     * Performance monitoring
     */
    public array $monitoring = [
        'enabled' => true,
        'log_hits' => false,        // Log cache hits
        'log_misses' => true,       // Log cache misses
        'log_slow_operations' => true, // Log operations > threshold
        'slow_threshold' => 100,    // milliseconds
        'metrics_ttl' => 3600,      // How long to keep metrics
    ];

    /**
     * Cache compression settings
     */
    public array $compression = [
        'enabled' => true,
        'algorithm' => 'gzip',      // gzip, deflate, brotli
        'level' => 6,               // Compression level (1-9)
        'min_size' => 1024,         // Only compress if larger than this
    ];

    /**
     * Memory optimization
     */
    public array $memory = [
        'max_memory_usage' => '256M',
        'cleanup_threshold' => 0.8,   // Start cleanup at 80% usage
        'cleanup_batch_size' => 100,  // Items to clean per batch
    ];

    /**
     * Development settings
     */
    public array $development = [
        'debug' => false,
        'log_queries' => false,
        'bypass_cache' => false,     // Bypass cache in development
        'force_refresh' => false,    // Force cache refresh
    ];
}