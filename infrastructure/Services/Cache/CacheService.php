<?php

namespace Infrastructure\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service for caching frequently accessed data
 */
class CacheService
{
    /**
     * Get data from cache or execute callback to retrieve and cache it
     *
     * @param string $key Cache key
     * @param int $ttl Time to live in seconds
     * @param callable $callback Function to execute if cache miss
     * @return mixed
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::error('Cache error: ' . $e->getMessage());
            // If caching fails, just execute the callback
            return $callback();
        }
    }

    /**
     * Store data in cache
     *
     * @param string $key Cache key
     * @param mixed $value Value to store
     * @param int $ttl Time to live in seconds
     * @return bool
     */
    public function put(string $key, $value, int $ttl): bool
    {
        try {
            return Cache::put($key, $value, $ttl);
        } catch (\Exception $e) {
            Log::error('Cache error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get data from cache
     *
     * @param string $key Cache key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::error('Cache error: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * Check if key exists in cache
     *
     * @param string $key Cache key
     * @return bool
     */
    public function has(string $key): bool
    {
        try {
            return Cache::has($key);
        } catch (\Exception $e) {
            Log::error('Cache error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove data from cache
     *
     * @param string $key Cache key
     * @return bool
     */
    public function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::error('Cache error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear entire cache
     *
     * @return bool
     */
    public function flush(): bool
    {
        try {
            return Cache::flush();
        } catch (\Exception $e) {
            Log::error('Cache error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate cache key for manga
     *
     * @param int|string $identifier Manga ID or slug
     * @return string
     */
    public function mangaKey($identifier): string
    {
        return 'manga:' . $identifier;
    }

    /**
     * Generate cache key for chapter
     *
     * @param int|string $identifier Chapter ID or slug
     * @return string
     */
    public function chapterKey($identifier): string
    {
        return 'chapter:' . $identifier;
    }

    /**
     * Generate cache key for user
     *
     * @param int|string $identifier User ID or username
     * @return string
     */
    public function userKey($identifier): string
    {
        return 'user:' . $identifier;
    }

    /**
     * Generate cache key for manga list
     *
     * @param string $type List type (featured, popular, latest, etc.)
     * @param array $params Additional parameters
     * @return string
     */
    public function mangaListKey(string $type, array $params = []): string
    {
        $key = 'manga:list:' . $type;
        
        if (!empty($params)) {
            $key .= ':' . md5(json_encode($params));
        }
        
        return $key;
    }
}
