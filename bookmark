<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/BookmarkEngine.php';

// Validate baseline command arguments
if ($argc < 3) {
    echo "\nUsage:\n  php cli.php add <website-url>\n  php cli.php search <keyword>\n\n";
    exit(1);
}

$action = $argv[1];
$queryParam = $argv[2];

try {
    $redis = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ]);
    $engine = new BookmarkEngine($redis);

    // --- HANDLE ACTION: SEARCH ---
    if ($action === 'search') {
        echo "Searching index for '$queryParam'...\n";
        $results = $engine->searchBookmarks($queryParam);

        if (empty($results)) {
            echo "\nNo matching bookmarks found.\n\n";
            exit;
        }

        $totalCount = count($results);
        $greenBadge = "\033[42;30m Total: " . $totalCount . " \033[0m";

        echo "\n============================================\n";
        echo "Search Result Matched:   " . $greenBadge . "\n\n";
        
        foreach ($results as $index => $item) {
            printf("[%d] Title:   %s\n", $index + 1, $item['title']);
            printf("    URL:     %s\n", $item['url']);
            if ($index < $totalCount - 1) {
                echo "\n";
            }
        }
        echo "============================================\n\n";
        exit;
    }

    // --- HANDLE ACTION: ADD ---
    if ($action === 'add') {
        if (!filter_var($queryParam, FILTER_VALIDATE_URL)) {
            echo "\nError: Invalid URL format.\n\n";
            exit(1);
        }

        echo "Processing URL...\n";
        $start = microtime(true);
        $result = $engine->getOrAdd($queryParam);
        $duration = (microtime(true) - $start) * 1000;

        $isHit = (strpos($result['source'], 'Hit') !== false);
        $badgeColor = $isHit ? "\033[42;30m" : "\033[41;37m"; 
        $resetColor = "\033[0m";
        $sourceBadge = $badgeColor . " " . $result['source'] . " " . $resetColor;

        echo "\n============================================\n";
        printf(" Title:        %s\n", $result['title']);
        printf(" Execution:    %s ms\n", round($duration, 2));
        printf(" Source:       %s\n", $sourceBadge);
        echo "============================================\n\n";
        exit;
    }

    echo "\nUnknown command action. Use 'add' or 'search'.\n\n";

} catch (Exception $e) {
    echo "\nSystem Error: Verify that backend database/cache systems are running.\n\n";
}