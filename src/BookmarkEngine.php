<?php
declare(strict_types=1);

use Elastic\Elasticsearch\ClientBuilder;

class BookmarkEngine {
    private object $redis; 
    private string $dbPath;
    private object $es;

    public function __construct(object $redis) {
        $this->redis = $redis;
        $this->dbPath = __DIR__ . '/../database.json';
        
        if (!file_exists($this->dbPath)) {
            file_put_contents($this->dbPath, json_encode([]));
        }

        $this->es = ClientBuilder::create()->build();
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
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
            ]
        ]);

        $html = @file_get_contents($url, false, $context);
        $title = 'Unknown Article';

        if ($html) {
            preg_match("/<title>(.*)<\/title>/is", $html, $matches);
            
            if (isset($matches[1])) {
                $cleanTitle = trim(preg_replace('/\s+/', ' ', $matches[1]));
                // Truncate to 60 characters max
                $title = mb_strimwidth($cleanTitle, 0, 60, '...');
            } else {
                $title = 'Untitled';
            }

            $wordCount = str_word_count(strip_tags($html));
        }
        $bookmarkData = [
            'id' => $id,
            'url' => $url,
            'title' => $title,
        ];

        // 3. Save to Permanent Storage (JSON File)
        $db = json_decode(file_get_contents($this->dbPath), true);
        $db[$id] = $bookmarkData;
        file_put_contents($this->dbPath, json_encode($db, JSON_PRETTY_PRINT));

        // 4. Save to Elasticsearch for Text Searching
        try {
            $this->es->index([
                'index' => 'bookmarks',
                'id'    => $id,
                'body'  => $bookmarkData
            ]);
        } catch (Exception $e) {
            echo "\n[ES Indexing Error]: " . $e->getMessage() . "\n";
        }

        // 5. Save to Redis Cache for next search 
        $this->redis->hmset($cacheKey, $bookmarkData);
        $this->redis->expire($cacheKey, 3600);

        $bookmarkData['source'] = 'Cache Miss (Scraped)';
        return $bookmarkData;
    }

    public function searchBookmarks(string $keyword): array {
        $params = [
            'index' => 'bookmarks',
            'body'  => [
                'query' => [
                    'match' => [
                        'title' => [
                            'query' => $keyword,
                            'fuzziness' => 'AUTO'
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = $this->es->search($params);
            $hits = $response['hits']['hits'];
            
            $results = [];
            foreach ($hits as $hit) {
                $results[] = $hit['_source'];
            }
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }
}