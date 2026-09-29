<?php

/**
 * Builds storage/app/private/image-contact.html — a labelled grid of every
 * downloaded product image, so the set can be eyeballed in one screenshot.
 * Photos outside the viewport need loading="eager" or the shot is full of blanks.
 */
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);

$root = dirname(__DIR__);
$dir = $root.'/storage/app/public/products';
$manifest = is_file($root.'/resources/data/product-images.json')
    ? (json_decode(file_get_contents($root.'/resources/data/product-images.json'), true) ?: [])
    : [];

$files = glob($dir.'/*.jpg') ?: [];
sort($files);

$cards = '';

foreach ($files as $file) {
    $slug = basename($file, '.jpg');
    $name = ucwords(str_replace('-', ' ', $slug));
    $entry = $manifest[$slug] ?? [];
    $license = $entry['license'] ?? '';

    $cards .= sprintf(
        '<figure class="card"><img src="../public/products/%s" alt="%s" loading="eager" decoding="sync">'
        .'<figcaption><strong>%s</strong><span>%s</span></figcaption></figure>',
        basename($file),
        htmlspecialchars($name),
        htmlspecialchars($slug),
        htmlspecialchars($license)
    );
}

$html = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="utf-8">
<style>
  body { margin: 0; padding: 16px; background: #EFF1F4; font: 13px/1.4 system-ui, sans-serif; color: #10161C; }
  h1 { font-size: 16px; margin: 0 0 12px; }
  .grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; }
  .card { background: #fff; border: 1px solid #DFE3E8; border-radius: 10px; overflow: hidden; }
  .card img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; background: #F7F8FA; }
  figcaption { padding: 5px 7px; border-top: 1px solid #EFF1F4; }
  figcaption strong { display: block; font-size: 11px; font-weight: 600; }
  figcaption span { font-size: 9px; color: #6E7883; }
</style></head>
<body>
<h1>Product image contact sheet — $count images</h1>
<div class="grid">$cards</div>
</body></html>
HTML;

$html = str_replace('$count', (string) count($files), $html);

$out = $root.'/storage/app/private/image-contact.html';
@mkdir(dirname($out), 0775, true);
file_put_contents($out, $html);

echo "wrote $out (".count($files)." images)\n";
