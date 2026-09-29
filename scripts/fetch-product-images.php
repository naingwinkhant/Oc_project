<?php

/**
 * Downloads a real photograph for every seeded product from Wikimedia Commons.
 *
 *   php scripts/fetch-product-images.php                     # fetch/repair all
 *   php scripts/fetch-product-images.php --slug=pineapple    # just these
 *   php scripts/fetch-product-images.php --unlock            # ignore locked titles
 *   php scripts/fetch-product-images.php --dry               # only report candidates
 *
 * The result is written to resources/data/product-images.json. ProductSeeder prefers
 * a .jpg listed there and falls back to the generated SVG when the entry is missing,
 * so seeding works offline.
 */
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);

$root = dirname(__DIR__);
$manifestPath = $root.'/resources/data/product-images.json';
$targetDir = $root.'/storage/app/public/products';

$options = getopt('', ['slug::', 'unlock', 'dry']);
$onlySlugs = isset($options['slug']) ? array_filter(explode(',', $options['slug'])) : null;

@mkdir($targetDir, 0775, true);
@mkdir(dirname($manifestPath), 0775, true);

$manifest = is_file($manifestPath) ? (json_decode(file_get_contents($manifestPath), true) ?: []) : [];

require __DIR__.'/product-image-queries.php';

$reject = [
    // document types
    'book', 'journal', 'article', 'magazine', 'newspaper', 'manuscript', 'volume',
    'edition', 'press', 'album', 'catalog',
    // maps & diagrams
    'map', 'diagram', 'chart', 'graph', 'scheme', 'layout', 'poster', 'sign',
    'logo', 'screenshot', 'icon', 'flag', 'coat of arms', 'banknote', 'stamp', 'plaque',
    'certificate', 'infographic', 'cross section',
    // people & places
    'portrait', 'statue', 'monument', 'church', 'temple', 'cemetery', 'tomb',
    'grave', 'memorial', 'station', 'restaurant', 'airport', 'street',
    // science / lab
    'specimen', 'herbarium', 'fossil', 'microscop', 'plasmolysis', 'histolog',
    'tissue', ' cells', 'stained', 'dissection', 'anatomy', 'skeleton',
    // not a clean product shot
    'recipe', 'plated', 'museum', 'festival', 'range of',
];

$userAgent = 'SupermarketGoodsHub/1.0 (Laravel demo dataset builder; dev@example.test)';

function http(string $url, string $userAgent, ?string $accept = 'application/json'): ?string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => $userAgent,
        CURLOPT_HTTPHEADER => [$accept],
    ]);
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $status === 200 ? (string) $body : null;
}

function save(string $url, string $path, string $userAgent): bool
{
    $body = http($url, $userAgent, 'image/*,*/*');

    if (! $body) {
        return false;
    }

    return file_put_contents($path, $body) !== false;
}

function scoreTitle(string $title, string $query, array $reject): float
{
    $name = preg_replace('/^File:/i', '', $title) ?? $title;
    $lower = strtolower($name);
    $score = 0.0;

    foreach ($reject as $word) {
        if (str_contains($lower, $word)) {
            $score -= 60;
        }
    }

    // Long titles are almost always scans, plates or documentation rather than photos.
    if (strlen($name) > 90) {
        $score -= 45;
    }

    if (preg_match('/\b1[89]\d\d\b|\b20\d\d\b/', $name)) {
        $score -= 6;
    }

    $words = array_values(array_filter(
        preg_split('/\W+/', strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [],
        fn ($w) => strlen($w) > 3
    ));

    foreach ($words as $word) {
        if (str_contains($lower, $word)) {
            $score += 9;
        }
    }

    // Commons titles are descriptive: the subject usually leads the name.
    $lead = strtolower(preg_split('/[\s,(\-—:]+/', $name)[0] ?? '');
    if ($words && $lead !== '' && in_array($lead, $words, true)) {
        $score += 30;
    }

    if (str_ends_with(strtolower($name), '.jpg') || str_ends_with(strtolower($name), '.jpeg')) {
        $score += 3;
    }

    return $score;
}

function searchCommons(string $query, string $userAgent, array $reject): array
{
    $url = 'https://commons.wikimedia.org/w/api.php?'.http_build_query([
        'action' => 'query',
        'generator' => 'search',
        'gsrsearch' => $query,
        'gsrnamespace' => 6,
        'gsrlimit' => 20,
        'prop' => 'imageinfo',
        'iiprop' => 'url|size|mime',
        'iiurlwidth' => 900,
        'format' => 'json',
    ]);

    $json = http($url, $userAgent);

    if (! $json) {
        return [];
    }

    $pages = json_decode($json, true)['query']['pages'] ?? [];
    $candidates = [];

    foreach ($pages as $page) {
        $info = $page['imageinfo'][0] ?? null;

        if (! $info || ! in_array($info['mime'], ['image/jpeg', 'image/png'], true)) {
            continue;
        }

        if (($info['width'] ?? 0) < 500 || ($info['height'] ?? 0) < 400) {
            continue;
        }

        $candidates[] = [
            'title' => $page['title'],
            'score' => scoreTitle($page['title'], $query, $reject),
        ];
    }

    usort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);

    return array_values(array_filter($candidates, fn ($c) => $c['score'] > 5));
}

function imageInfo(string $title, string $userAgent): ?array
{
    $url = 'https://commons.wikimedia.org/w/api.php?'.http_build_query([
        'action' => 'query',
        'titles' => $title,
        'prop' => 'imageinfo',
        'iiprop' => 'url|size|mime|extmetadata',
        'iiurlwidth' => 900,
        'format' => 'json',
    ]);

    $json = http($url, $userAgent);

    if (! $json) {
        return null;
    }

    $page = array_values(json_decode($json, true)['query']['pages'] ?? [])[0] ?? null;

    return $page['imageinfo'][0] ?? null;
}

$overrides = productImageOverrides();

/**
 * Commons metadata is a mix of HTML and entities; strip it down to readable text.
 * "PhotoFizza &amp; Ifz" must not survive as literal markup.
 */
function cleanMetadata(string $value): string
{
    $text = $value;

    // Commons sometimes double-encodes, so decode until it settles.
    for ($i = 0; $i < 3; $i++) {
        $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($decoded === $text) {
            break;
        }

        $text = $decoded;
    }

    $text = strip_tags($text);
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
    $text = preg_replace('/^photo\s*[:\-]\s*/iu', '', $text) ?? $text;

    return trim($text);
}

$slugs = $onlySlugs ?? array_keys(productImageQueries());
$ok = 0;
$failed = [];
$index = 0;

foreach ($slugs as $slug) {
    $query = productImageQueries()[$slug] ?? $slug;
    $index++;

    // Precedence: hand-picked override, then a previously locked title
    // (unless --unlock), then relevance search. Overrides are always honoured.
    $override = $overrides[$slug] ?? null;
    $locked = $override ?: (isset($options['unlock']) ? null : ($manifest[$slug]['file'] ?? null));

    if ($override && ! isset($options['unlock']) && is_file($targetDir.'/'.$slug.'.jpg')
        && ($manifest[$slug]['file'] ?? null) === $override) {
        printf("keep  %-28s override\n", $slug);

        continue;
    }

    if (! $override && ! isset($options['unlock']) && $locked && is_file($targetDir.'/'.$slug.'.jpg')) {
        printf("skip  %-28s locked\n", $slug);

        continue;
    }

    $titles = $locked ? [$locked] : array_column(searchCommons($query, $userAgent, $reject), 'title');

    if (! $titles) {
        $failed[$slug] = 'no candidate';
        printf("FAIL  %-28s no candidate\n", $slug);

        continue;
    }

    $placed = false;

    foreach (array_slice($titles, 0, 3) as $title) {
        usleep(1200000);
        $info = imageInfo($title, $userAgent);

        if (! $info || ! in_array($info['mime'], ['image/jpeg', 'image/png'], true)) {
            continue;
        }

        usleep(400000);

        if (! save($info['thumburl'] ?? $info['url'], $targetDir.'/'.$slug.'.jpg', $userAgent)) {
            continue;
        }

        if (isset($options['dry'])) {
            @unlink($targetDir.'/'.$slug.'.jpg');
            printf("cand  %-28s %s\n", $slug, preg_replace('/^File:/', '', $title));
            $placed = true;
            break;
        }
        $meta = $info['extmetadata'] ?? [];

        $manifest[$slug] = [
            'file' => $title,
            'query' => $query,
            'source' => $info['descriptionurl'] ?? $info['url'],
            'author' => cleanMetadata($meta['Artist']['value'] ?? ''),
            'license' => cleanMetadata($meta['LicenseShortName']['value'] ?? 'CC'),
        ];

        printf("%-5s %-28s %s\n", $override ? 'over' : ($locked ? 'lock' : 'ok'), $slug, preg_replace('/^File:/', '', $title));
        $placed = true;
        $ok++;
        break;
    }

    if (! $placed) {
        $failed[$slug] = 'download failed';
        printf("FAIL  %-28s download failed\n", $slug);
    }

    if (! isset($options['dry']) && $index % 10 === 0) {
        ksort($manifest);
        file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

if (! isset($options['dry'])) {
    ksort($manifest);
    file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

echo PHP_EOL."ok: $ok   failed: ".count($failed).PHP_EOL;

if ($failed) {
    echo 'failures: '.implode(', ', array_keys($failed)).PHP_EOL;
}
