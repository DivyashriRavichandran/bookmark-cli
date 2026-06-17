<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/BookmarkEngine.php';

if ($argc < 3 || $argv[1] !== 'add') {
    echo "\nUsage: php cli.php add <website-url>\n\n";
    exit(1);
}

$url = $argv[2];

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo "\nError: Invalid URL format.\n\n";
    exit(1);
}

try {
    $redis = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ]);

    echo "Processing URL...\n";
    
    $engine = new BookmarkEngine($redis);
    $start = microtime(true);
    $result = $engine->getOrAdd($url);
    $duration = (microtime(true) - $start) * 1000;

    // --- Determine Badge Color Based on Source ---
    $isHit = (strpos($result['source'], 'Hit') !== false);
    
    if ($isHit) {
        $badgeColor = "\033[42;30m"; // Green background, Black text 
    } else {
        $badgeColor = "\033[41;37m"; // Red background, White text 
    }
    $resetColor = "\033[0m";
    $sourceBadge = $badgeColor . " " . $result['source'] . " " . $resetColor;

    // --- Output Formatting ---
    echo "\n============================================\n";
    printf(" Title:        %s\n", $result['title']);
    printf(" Reading Time: %s\n", $result['reading_time']);
    printf(" Execution:    %s ms\n", round($duration, 2));
    printf(" Source:       %s\n", $sourceBadge);

    echo "============================================\n\n";

} catch (Exception $e) {
    echo "\nSystem Error: Could not connect to Redis server.\n\n";
}