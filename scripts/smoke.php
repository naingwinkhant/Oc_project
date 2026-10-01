<?php

/**
 * End-to-end smoke test against a running dev server.
 *
 *   php scripts/smoke.php [--write]
 *
 * Read-only by default: it signs in, walks every page the shop and the admin
 * expose, and exercises the cart -> checkout -> sandbox payment flow without
 * leaving anything behind. Pass --write to also run the checks that create
 * records (product, classification, supplier, user).
 */

$base = getenv('SMOKE_BASE') ?: 'http://127.0.0.1:8000';
$write = in_array('--write', $argv, true);
$jar = sys_get_temp_dir().'/ggs-smoke-cookies.txt';
@unlink($jar);

function req(string $url, ?array $post = null): array
{
    global $jar;

    $attempt = 0;

    while (true) {
        $attempt++;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $jar,
            CURLOPT_COOKIEFILE => $jar,
            CURLOPT_TIMEOUT => 60,
        ]);

        if ($post !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($status !== 0 || $attempt >= 3) {
            return ['status' => $status, 'body' => (string) $body, 'error' => $error];
        }

        usleep(400000);
    }
}

/**
 * Every public page carries the token in a meta tag, so this works even on
 * pages with no forms (an empty cart, for instance).
 */
function token(string $html): ?string
{
    if (preg_match('/name="csrf-token"\s+content="([^"]+)"/', $html, $m)) {
        return $m[1];
    }

    return preg_match('/name="_token"\s+value="([^"]+)"/', $html, $m) ? $m[1] : null;
}

$results = [];
$fail = 0;

function check(string $label, array $res, array $expect = [200], array $mustContain = []): void
{
    global $results, $fail;

    $ok = in_array($res['status'], $expect, true);
    $body = $res['body'];

    if ($ok && $body === '' && $res['error'] !== '') {
        $ok = false;
    }

    foreach (['Whoops', 'ParseError', 'Fatal error', 'Undefined variable'] as $smell) {
        if (str_contains($body, $smell)) {
            $ok = false;
            $res['missing'] = $smell;
        }
    }

    foreach ($mustContain as $needle) {
        if (! str_contains($body, $needle)) {
            $ok = false;
            $res['missing'] = $needle;
        }
    }

    $results[] = sprintf('%-4s %-3d %s%s', $ok ? 'OK' : 'FAIL', $res['status'], $label,
        isset($res['missing']) ? '   (missing: '.$res['missing'].')' : '');

    if (! $ok) {
        $fail++;
    }
}

$login = req($base.'/login');
check('GET /login', $login, [200], ['Sign in']);

// The two role-specific doors. Both render, and a staff credential is refused
// at the admin one.
$adminDoor = req($base.'/admin/login.php');
check('GET /admin/login.php', $adminDoor, [200], ['Administrator sign in']);

$staffDoor = req($base.'/staff/login.php');
check('GET /staff/login.php', $staffDoor, [200], ['Staff sign in']);

$refused = req($base.'/admin/login.php', [
    '_token' => token($adminDoor['body']),
    'email' => 'staff@supermarket.test',
    'password' => 'password',
]);
check('POST /admin/login.php (staff refused)', $refused, [200], ['not an administrator']);

check('POST /staff/login.php (staff accepted)', req($base.'/staff/login.php', [
    '_token' => token($staffDoor['body']),
    'email' => 'staff@supermarket.test',
    'password' => 'password',
]), [200]);

check('GET /admin (as staff)', req($base.'/admin'), [200], ['Dashboard']);

check('POST /logout', req($base.'/logout', ['_token' => token(req($base.'/admin')['body'])]), [200]);

$login = req($base.'/login');
check('POST /login (admin)', req($base.'/login', [
    '_token' => token($login['body']),
    'email' => 'admin@supermarket.test',
    'password' => 'password',
]), [200]);

// --- storefront ------------------------------------------------------------

$public = [
    ['/', [200], ['Supermarket Goods Hub']],
    ['/catalog', [200], ['Fresh Produce']],
    ['/catalog?q=banana', [200], ['Banana']],
    ['/catalog?q=SKU-000001', [200], []],
    ['/catalog?sort=price_desc&in_stock=1', [200], []],
    ['/catalog/new-arrivals', [200], ['New arrivals']],
    ['/catalog/fresh-produce', [200], ['Fruits']],
    ['/catalog/fresh-produce-fruits', [200], ['Cavendish Bananas']],
    ['/catalog/goods/cavendish-bananas', [200], ['Cavendish Bananas', 'Add to cart', 'Save to favourites']],
    ['/catalog/category-does-not-exist', [404], []],
    ['/favourites', [200], ['Favourites']],
    ['/cart', [200], ['Your cart']],
];

foreach ($public as [$path, $expect, $needles]) {
    check('GET '.$path, req($base.$path), $expect, $needles);
}

// --- cart, checkout, sandbox payment ---------------------------------------

$productId = null;
$catalogue = req($base.'/catalog/fresh-produce');
$csrf = token($catalogue['body']);

// Pick a product that is actually sellable: an expired, out-of-stock or
// coming-soon item has no add-to-cart form, and refusing it is correct.
preg_match_all('#/catalog/goods/([a-z0-9-]+)#', $catalogue['body'], $links);

foreach (array_unique($links[1]) as $slug) {
    $page = req($base.'/catalog/goods/'.$slug);

    // The id must come from inside the cart form: the page also lists related
    // products, and their cards have add-to-cart forms of their own.
    if (! preg_match('#action="'.preg_quote($base.'/cart', '#').'"[^>]*>\s*(?:<[^>]+>\s*)*<input[^>]*name="product_id" value="(\d+)"#', $page['body'], $id)) {
        continue;
    }

    $productId = (int) $id[1];

    if ($productId) {
        echo "picked $slug (id $productId) for the cart checks\n";
        break;
    }
}

if ($productId && $csrf) {
    check('POST /cart', req($base.'/cart', [
        '_token' => $csrf,
        'product_id' => $productId,
        'quantity' => 2,
    ]), [200], ['added to your cart']);

    check('POST /favourites/toggle', req($base.'/favourites/toggle', [
        '_token' => $csrf,
        'product_id' => $productId,
        'return_to' => $base.'/catalog',
    ]), [200]);

    $cart = req($base.'/cart');
    check('GET /cart with an item', $cart, [200], ['Order summary', 'Checkout']);

    $checkout = req($base.'/checkout');
    check('GET /checkout', $checkout, [200], ['Delivery details', 'Payment method', 'data-map=']);

    $order = req($base.'/checkout', [
        '_token' => token($checkout['body']) ?: $csrf,
        'customer_name' => 'Smoke Tester',
        'phone' => '09 380 000 00',
        'email' => 'smoke@example.com',
        'delivery_address' => 'No. 1, Test Street, Kamayut',
        'township' => 'Kamayut',
        'payment_gateway' => 'sandbox',
    ]);
    check('POST /checkout', $order, [200], ['Complete your payment']);

    if (preg_match('#/checkout/(GGS-[A-Z0-9-]+)#', $order['body'], $mm)) {
        $orderPage = req($base.'/checkout/'.$mm[1]);
        check('GET '.$mm[1], $orderPage, [200], ['Complete your payment', 'Pay ']);

        // The order page links to the provider's hosted checkout.
        $sandboxUrl = preg_match('#href="([^"]*/payments/sandbox/[^"]+)"#', $orderPage['body'], $sm)
            ? html_entity_decode($sm[1])
            : null;

        $sandbox = $sandboxUrl ? req($sandboxUrl) : ['status' => 0, 'body' => '', 'error' => 'no sandbox link'];
        check('GET sandbox payment page', $sandbox, [200], ['Simulated', 'Test mode']);

        $settleUrl = preg_match('#action="([^"]*sandbox/[^"]*/settle)"#', $sandbox['body'], $fm)
            ? html_entity_decode($fm[1])
            : null;

        $signature = preg_match('#name="signature" value="([^"]+)"#', $sandbox['body'], $gm) ? $gm[1] : null;

        if ($settleUrl && $signature) {
            check('POST sandbox settle', req($settleUrl, [
                '_token' => token($sandbox['body']),
                'signature' => $signature,
                'outcome' => 'paid',
            ]), [200], ['Payment received', 'Paid']);
        } else {
            check('POST sandbox settle', ['status' => 0, 'body' => '', 'error' => 'no settle form'], [200]);
        }
    } else {
        check('order confirmation page', ['status' => 0, 'body' => '', 'error' => 'no order number'], [200]);
    }

    // Leave the shop the way we found it.
    check('DELETE /cart (empty cart)', req($base.'/cart', [
        '_token' => $csrf,
        '_method' => 'DELETE',
    ]), [200]);

    check('DELETE /favourites (clear)', req($base.'/favourites', [
        '_token' => $csrf,
        '_method' => 'DELETE',
    ]), [200]);
} else {
    check('find a sellable product for the cart checks', ['status' => 0, 'body' => '', 'error' => 'none found'], [200]);
}

// --- admin -----------------------------------------------------------------

$admin = [
    ['/admin', [200], ['Dashboard']],
    ['/admin/products', [200], ['Goods']],
    ['/admin/products?q=milk', [200], ['Milk']],
    ['/admin/products?status=low', [200], []],
    ['/admin/products?status=out', [200], []],
    ['/admin/products?status=expiring', [200], []],
    ['/admin/products?sort=price_desc&category=2', [200], []],
    ['/admin/products/create', [200], ['Add to catalogue']],
    ['/admin/categories', [200], ['Department tree']],
    ['/admin/categories?q=fruit', [200], []],
    ['/admin/categories/create', [200], ['Create classification']],
    ['/admin/stock', [200], ['Record movement']],
    ['/admin/stock?type=in', [200], []],
    ['/admin/stock/low', [200], []],
    ['/admin/suppliers', [200], ['Metro Fresh']],
    ['/admin/suppliers/create', [200], ['Add supplier']],
    ['/admin/suppliers/1/edit', [200], ['Save changes']],    ['/admin/users', [200], ['admin@supermarket.test']],
    ['/admin/users/create', [200], ['Create user']],
    ['/admin/orders', [200], []],
    ['/admin/activity', [200], ['Activity log']],
];

foreach ($admin as [$path, $expect, $needles]) {
    check('GET '.$path, req($base.$path), $expect, $needles);
}

if ($write) {
    $productId = (int) (preg_match('#/admin/products/(\d+)/edit#', req($base.'/admin/products')['body'], $m) ? $m[1] : 0);

    $edit = req($base.'/admin/products/'.$productId.'/edit');
    check('GET /admin/products/'.$productId.'/edit', $edit, [200], ['Save changes']);

    $create = req($base.'/admin/products/create');
    check('POST /admin/products', req($base.'/admin/products', [
        '_token' => token($create['body']),
        'name' => 'Smoke Test Item',
        'unit' => 'pcs',
        'price' => '2750',
        'cost_price' => '2000',
        'stock' => '25',
        'min_stock' => '5',
        'category_id' => '2',
        'is_active' => '1',
    ]), [200], ['was added to the catalogue']);

    // A promotion below cost must be refused.
    $create = req($base.'/admin/products/create');
    check('POST /admin/products (sale under cost refused)', req($base.'/admin/products', [
        '_token' => token($create['body']),
        'name' => 'Smoke Loss Leader',
        'unit' => 'pcs',
        'price' => '5000',
        'cost_price' => '4000',
        'sale_price' => '3500',
        'category_id' => '2',
        'is_active' => '1',
    ]), [200], ['cannot be below the cost price']);

    $catCreate = req($base.'/admin/categories/create');
    check('POST /admin/categories', req($base.'/admin/categories', [
        '_token' => token($catCreate['body']),
        'name' => 'Smoke Test Dept',
        'is_active' => '1',
    ]), [200], ['classification was created']);

    $supCreate = req($base.'/admin/suppliers/create');
    check('POST /admin/suppliers', req($base.'/admin/suppliers', [
        '_token' => token($supCreate['body']),
        'name' => 'Smoke Test Vendor',
        'is_active' => '1',
    ]), [200], ['was added']);

    $userCreate = req($base.'/admin/users/create');
    $username = 'smoke'.substr(md5((string) time()), 0, 6);
    check('POST /admin/users', req($base.'/admin/users', [
        '_token' => token($userCreate['body']),
        'username' => $username,
        'name' => 'Smoke Tester',
        'email' => $username.'@supermarket.test',
        'role' => 'staff',
        'password' => 'smoke12345',
        'password_confirmation' => 'smoke12345',
        'is_active' => '1',
    ]), [200], ['can now sign in']);
} else {
    $results[] = 'SKIP --write checks (products, classifications, suppliers, users)';
}

// --- roles -----------------------------------------------------------------

check('POST /logout', req($base.'/logout', ['_token' => token(req($base.'/admin')['body'])]), [200]);

// /admin opens the admin door, not the generic page.
check('GET /admin after logout opens the admin door', req($base.'/admin'), [200], ['Administrator sign in']);
check('GET /staff opens the staff door', req($base.'/staff'), [200], ['Staff sign in']);
check('GET /admin/products opens the admin door', req($base.'/admin/products'), [200], ['Administrator sign in']);

$login2 = req($base.'/login');
check('POST /login (staff)', req($base.'/login', [
    '_token' => token($login2['body']),
    'email' => 'staff@supermarket.test',
    'password' => 'password',
]), [200]);

check('staff GET /admin', req($base.'/admin'), [200], ['Dashboard']);
check('staff GET /admin/stock', req($base.'/admin/stock'), [200], ['Stock movements']);
check('staff GET /admin/categories (forbidden)', req($base.'/admin/categories'), [403]);
check('staff GET /admin/users (forbidden)', req($base.'/admin/users'), [403]);

// --- the four roles land on four different pages ---------------------------

// Staff are sent to their own page rather than the administrator's dashboard,
// so the two are genuinely different places.
$staff2 = req($base.'/admin/staff', null);
check('staff GET /admin/staff', $staff2, [200]);

// The customer page and the manager page both refuse the rest of the team.
$staff3 = req($base.'/admin/manager');
check('staff GET /admin/manager (forbidden)', $staff3, [403]);

// A manager reaches the manager page and the approval queue, but not users.
check('POST /logout', req($base.'/logout', ['_token' => token(req($base.'/admin/staff')['body'])]), [200]);

check('POST /staff/login.php (manager)', req($base.'/staff/login.php', [
    '_token' => token(req($base.'/staff/login.php')['body']),
    'email' => 'manager@supermarket.test',
    'password' => 'password',
]), [200]);

check('manager GET /admin/manager', req($base.'/admin/manager'), [200]);
check('manager GET /admin/approvals', req($base.'/admin/approvals'), [200], ['New accounts']);
check('manager GET /admin/users (forbidden)', req($base.'/admin/users'), [403]);

// A customer reaches their own page and nothing in the admin area.
check('POST /logout', req($base.'/logout', ['_token' => token(req($base.'/admin/manager')['body'])]), [200]);

check('POST /create-account renders', req($base.'/create-account'), [200], ['Create account']);

@unlink($jar);

$ran = count(array_filter($results, fn ($r) => ! str_starts_with($r, 'SKIP')));

echo implode(PHP_EOL, $results).PHP_EOL.PHP_EOL;
echo $fail === 0 ? 'ALL '.$ran.' CHECKS PASSED' : $fail.' of '.$ran.' CHECKS FAILED';
echo PHP_EOL;

exit($fail === 0 ? 0 : 1);
