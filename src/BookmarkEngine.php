<?php
declare(strict_types=1);

class BookmarkEngine {
    private object $redis; 
    private string $dbPath;

    public function __construct(object $redis) {
        $this->redis = $redis;
        $this->dbPath = __DIR__ . '/../database.json';
        
        if (!file_exists($this->dbPath)) {
            file_put_contents($this->dbPath, json_encode([]));
        }
    }

    public function getOrAdd(string $url): array {
        $id = md5($url);
        $cacheKey = "bookmark:" . $id;

        // 1. Fetching from Redis Cache 
        $cachedData = $this->redis->hgetall($cacheKey);
        if (!empty($cachedData)) {
            $cachedData['source'] = 'Cache Hit (Redis RAM)';
            return $cachedData;
        }

        // 2. Cache Miss: Scrape data live from the web
        $html = @file_get_contents($url);
        $title = 'Unknown Article';
        $readingTime = '1';

        if ($html) {
            preg_match("/<title>(.*)<\/title>/i", $html, $matches);
            $title = isset($matches[1]) ? trim($matches[1]) : 'Untitled';
            $wordCount = str_word_count(strip_tags($html));
            $readingTime = (string)max(1, (int)ceil($wordCount / 200)); 
        }

        $bookmarkData = [
            'id' => $id,
            'url' => $url,
            'title' => $title,
            'reading_time' => $readingTime . ' min'
        ];

        // 3. Save to Storage (JSON File)
        $db = json_decode(file_get_contents($this->dbPath), true);
        $db[$id] = $bookmarkData;
        file_put_contents($this->dbPath, json_encode($db, JSON_PRETTY_PRINT));

        // 4. Save to Redis Cache for next time 
        $this->redis->hmset($cacheKey, $bookmarkData);
        $this->redis->expire($cacheKey, 3600);

        $bookmarkData['source'] = 'Cache Miss (Fresh Scrape)';
        return $bookmarkData;
    }
}