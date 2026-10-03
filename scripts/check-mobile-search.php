<?php

/**
 * Reads the rendered header at a phone width, straight out of the HTML, so the
 * search field's classes are checked without needing a browser.
 *
 *   php scripts/check-mobile-search.php
 */

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Asked for as a real request, so the controller supplies everything the view
// needs. Rendering the view on its own would trip over variables it normally
// receives from there.
$html = (string) app()->handle(Illuminate\Http\Request::create('/catalog', 'GET'))->getContent();

// Every search form on the page, with the classes that decide their width.
preg_match_all('#<form[^>]*role="search"[^>]*>(.*?)</form>#s', $html, $forms);

echo 'search forms found: '.count($forms[0]).PHP_EOL;

foreach ($forms[0] as $index => $form) {
    preg_match('#<form[^>]*class="([^"]*)"#', $form, $class);

    // The form may carry no class of its own: on a phone the width comes from
    // the wrapper it sits in, so the wrapper is read too.
    $wrapper = '';

    if ($index > 0) {
        $before = substr($html, 0, strpos($html, $form));
        preg_match_all('#<div[^>]*class="([^"]*)"[^>]*>\s*(?:<a[^>]*>[^<]*</a>\s*)?$#', $before, $wrappers);

        $wrapper = end($wrappers[1]) ?: '';
    }

    $own = $class[1] ?? '';
    $scope = $own !== '' ? $own : $wrapper;

    echo PHP_EOL.'form '.($index + 1).PHP_EOL;
    echo '  form class: '.($own !== '' ? $own : '(none)').PHP_EOL;
    echo '  wrapper: '.($wrapper !== '' ? $wrapper : '(none)').PHP_EOL;

    // sm:hidden marks the phone row, sm:block the one that shares the top bar.
    $isPhoneOnly = str_contains($scope, 'sm:hidden');
    $isDesktopOnly = str_contains($scope, 'hidden') && str_contains($scope, 'sm:block');

    echo '  purpose: '.($isPhoneOnly ? 'phone row (full width)' : ($isDesktopOnly ? 'desktop row (shares the top bar)' : 'always shown')).PHP_EOL;

    preg_match('#<input[^>]*type="search"[^>]*>#', $form, $input);
    preg_match('#class="([^"]*)"#', $input[0] ?? '', $inputClass);
    echo '  input: '.($inputClass[1] ?? '(none)').PHP_EOL;
}

echo PHP_EOL;

// The phone row must sit outside the max-width bar so it can run edge to edge.
echo 'has a phone-only row: '.(str_contains($html, 'border-t border-ink-200 bg-surface px-4 py-2 sm:hidden') ? 'yes' : 'no').PHP_EOL;
echo 'has a desktop-only row: '.(str_contains($html, 'sm:block sm:max-w-md') ? 'yes' : 'no').PHP_EOL;
echo 'mobile menu still present: '.(str_contains($html, 'id="mobile-menu"') ? 'yes' : 'no').PHP_EOL;
echo 'search field is labelled: '.(str_contains($html, 'aria-label="Search goods"') ? 'yes' : 'no').PHP_EOL;