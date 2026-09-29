<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__.'/../resources/views', FilesystemIterator::SKIP_DOTS)
);

$tmp = sys_get_temp_dir().'/view-lint';
@mkdir($tmp);

$broken = 0;

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $compiled = app('blade.compiler')->compileString(file_get_contents($path));
    $target = $tmp.'/lint.php';
    file_put_contents($target, $compiled);

    exec('php -l '.escapeshellarg($target).' 2>&1', $out, $code);

    if ($code !== 0) {
        $broken++;
        echo 'BROKEN: '.str_replace(__DIR__.'/../', '', $path).PHP_EOL;
        echo '        '.implode(' ', $out).PHP_EOL;
    }

    $out = [];
}

echo $broken === 0 ? 'All views compile cleanly.' : $broken.' broken view(s).'.PHP_EOL;
