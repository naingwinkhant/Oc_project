<?php

/**
 * Prints raw Commons search results so a specific file title can be hand-picked
 * and locked in resources/data/product-images.json.
 *
 *   php scripts/commons-search.php "beef brisket raw"
 */
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);

$query = $argv[1] ?? '';
$limit = (int) ($argv[2] ?? 8);

$url = 'https://commons.wikimedia.org/w/api.php?'.http_build_query([
    'action' => 'query',
    'generator' => 'search',
    'gsrsearch' => $query,
    'gsrnamespace' => 6,
    'gsrlimit' => $limit,
    'prop' => 'imageinfo',
    'iiprop' => 'url|size|mime',
    'iiurlwidth' => 900,
    'format' => 'json',
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => 'SupermarketGoodsHub/1.0 (dev@example.test)',
    CURLOPT_TIMEOUT => 45,
]);
$json = curl_exec($ch);
curl_close($ch);

$pages = json_decode((string) $json, true)['query']['pages'] ?? [];

foreach ($pages as $page) {
    $info = $page['imageinfo'][0] ?? null;

    if (! $info || ! in_array($info['mime'], ['image/jpeg', 'image/png'], true)) {
        continue;
    }

    printf("%-6s %sx%s  %s\n", '', $info['width'], $info['height'], $page['title']);
}
