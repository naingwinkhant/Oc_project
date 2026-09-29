<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Store identity
    |--------------------------------------------------------------------------
    */

    'name' => env('SHOP_NAME', 'Golden Gate Supermarket'),
    'tagline' => env('SHOP_TAGLINE', 'Fresh goods, fairly priced, every day.'),
    'phone' => env('SHOP_PHONE', '09 380 000 00'),
    'address' => env('SHOP_ADDRESS', 'No. 12, Baho Road, Kamayut, Yangon'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    | The Myanmar kyat (MMK) has no minor unit in circulation, so every amount is
    | a whole number and is rounded to the nearest 50 kyat on the shelf.
    */

    'currency' => [
        'code' => env('SHOP_CURRENCY', 'MMK'),
        'symbol' => env('SHOP_CURRENCY_SYMBOL', 'Ks'),
        'decimals' => 0,
        'round_to' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Delivery
    |--------------------------------------------------------------------------
    | Charges are zoned, not flat: the same basket costs less to send across
    | town than it does upcountry. Every township in the list must belong to
    | exactly one zone.
    */

    'delivery' => [

        'fee' => (int) env('SHOP_DELIVERY_FEE', 2500),
        'free_over' => (int) env('SHOP_DELIVERY_FREE_OVER', 150000),

        'zones' => [
            [
                'key' => 'inner',
                'label' => 'Inner Yangon',
                'fee' => (int) env('SHOP_DELIVERY_FEE_INNER', 1500),
                'eta' => 'Same day',
                'townships' => [
                    'Kamayut', 'Hlaing', 'Sanchaung', 'Thingangyun', 'Dagon',
                    'Insein', 'Htantabin', 'Seikkan', 'Ahlone', 'Kyauktada',
                ],
            ],
            [
                'key' => 'metro',
                'label' => 'Greater Yangon',
                'fee' => (int) env('SHOP_DELIVERY_FEE_METRO', 2500),
                'eta' => '1–2 days',
                'townships' => [
                    'North Dagon', 'South Dagon', 'East Dagon', 'Hlainggyi', 'Shwepyitar',
                    'Pyay', 'Htantabin East', 'Bahan', 'Kyanggyi', 'Si Han', 'Thanlyin',
                ],
            ],
            [
                'key' => 'upcountry',
                'label' => 'Other townships',
                'fee' => (int) env('SHOP_DELIVERY_FEE_UPCOUNTRY', 5000),
                'eta' => '2–4 days',
                'townships' => [
                    'Mandalay', 'Naypyidaw', 'Bago', 'Pathein', 'Mawlamyine',
                    'Taunggyi', 'Myitkyina', 'Mawlamyine', 'Hpa-An', 'Myeik',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Freshness
    |--------------------------------------------------------------------------
    | How close to the expiry date an item starts being flagged on the goods
    | list, the dashboard and the catalogue.
    */

    'freshness' => [
        'expiring_soon_days' => (int) env('SHOP_EXPIRING_SOON_DAYS', 30),
        'new_arrival_days' => (int) env('SHOP_NEW_ARRIVAL_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    'orders' => [
        'prefix' => env('SHOP_ORDER_PREFIX', 'GGS'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Homepage carousel
    |--------------------------------------------------------------------------
    | The product slides are picked automatically (promotions and new arrivals
    | that can actually be bought, with a real photograph). PROMO_VIDEO is an
    | optional advertisement that becomes the first slide: drop an .mp4 or
    | .webm in storage/app/public/promo/ and point this at it. It is ignored
    | until the file exists, so a missing upload breaks nothing.
    */

    'promo' => [
        'slides' => (int) env('PROMO_SLIDES', 5),
        'autoplay_ms' => (int) env('PROMO_AUTOPLAY_MS', 6000),
        'video' => env('PROMO_VIDEO'),
        'video_poster' => env('PROMO_VIDEO_POSTER'),
        'video_title' => env('PROMO_VIDEO_TITLE', 'This week at the store'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Homepage advertising
    |--------------------------------------------------------------------------
    | The wording in the banner above the catalogue grid. Edit these in .env
    | rather than in the view when you want a seasonal campaign.
    */

    'hero' => [
        'headline' => env('SHOP_HERO_HEADLINE', 'Fill your basket, not your budget'),
        'subline' => env('SHOP_HERO_SUBLINE', 'Fresh produce, meat and pantry staples with weekly promotions, delivered across Yangon and beyond.'),
        'cta' => env('SHOP_HERO_CTA', 'Shop the weekly deals'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    | "sandbox" is a local simulator used whenever no live credentials are
    | configured, so the whole cart -> checkout -> payment flow can be run and
    | tested without a merchant account. Set PAYMENTS_DRIVER=live (and fill in
    | the credentials above each gateway) to talk to the real providers.
    */

    'payments' => [

        'driver' => env('PAYMENTS_DRIVER', 'sandbox'),

        'sandbox' => [
            'enabled' => env('PAYMENTS_SANDBOX_ENABLED', true),
        ],

        'kbzpay' => [
            'enabled' => env('KBZPAY_ENABLED', false),
            'precreate_url' => env('KBZPAY_PRECREATE_URL', 'https://api.kbzpay.com/payment/gateway/precreate'),
            'query_url' => env('KBZPAY_QUERYORDER_URL', 'https://api.kbzpay.com/payment/gateway/queryorder'),
            'merchant_code' => env('KBZPAY_MERCH_CODE'),
            'merchant_id' => env('KBZPAY_MERCHANT_ID'),
            'app_id' => env('KBZPAY_APP_ID'),
            'secret' => env('KBZPAY_SECRET'),
            'trade_type' => env('KBZPAY_TRADE_TYPE', 'APP'),
            'timeout' => 30,
        ],

        'ayapay' => [
            'enabled' => env('AYAPAY_ENABLED', false),
            'payment_url' => env('AYAPAY_PAYMENT_URL', 'https://api.ayapay.com.mm/v1/payment'),
            'status_url' => env('AYAPAY_STATUS_URL', 'https://api.ayapay.com.mm/v1/payment/status'),
            'merchant_id' => env('AYAPAY_MERCHANT_ID'),
            'merchant_key' => env('AYAPAY_MERCHANT_KEY'),
            'timeout' => 30,
        ],

        'wavemoney' => [
            'enabled' => env('WAVEMONEY_ENABLED', false),
            'payment_url' => env('WAVEMONEY_PAYMENT_URL', 'https://api.wave.com/payments/sessions'),
            'merchant_id' => env('WAVEMONEY_MERCHANT_ID'),
            'api_key' => env('WAVEMONEY_API_KEY'),
            'api_secret' => env('WAVEMONEY_API_SECRET'),
            'timeout' => 30,
        ],

        'uabpay' => [
            'enabled' => env('UABPAY_ENABLED', false),
            'login_url' => env('UABPAY_LOGIN_URL', 'https://uab.com.mm/api/login'),
            'payment_url' => env('UABPAY_PAYMENT_URL', 'https://uat-uab.com/Payment'),
            'merchant_id' => env('UABPAY_MERCHANT_ID'),
            'access_key' => env('UABPAY_ACCESS_KEY'),
            'secret_key' => env('UABPAY_SECRET_KEY'),
            'channel' => env('UABPAY_MERCHANT_CHANNEL', 'WEB'),
            'expire' => (int) env('UABPAY_PAYMENT_EXPIRE', 300),
            'timeout' => 30,
        ],
    ],

];
