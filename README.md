# Supermarket Goods Hub

A goods-classification and inventory system for a supermarket, built with **Laravel 12**,
**MySQL (MariaDB)** and **Tailwind CSS v4**. Fully responsive (mobile + desktop), with a
public storefront (cart, checkout, favourites, zoned delivery, Myanmar payment gateways)
and a role-based admin panel.

---

## Requirements

| Tool | Version used |
| --- | --- |
| PHP | 8.2+ with `pdo_mysql`, `mbstring`, `fileinfo` |
| Composer | 2.x |
| Node.js | 20+ |
| MySQL / MariaDB | MySQL 8+ or MariaDB 10.4+ (XAMPP works) |

> The **GD extension is not required** — the demo seeders generate SVG product images.

---

## Install

```bash
# 1. dependencies
composer install
npm install

# 2. environment
copy .env.example .env          # Windows
cp .env.example .env            # macOS / Linux
php artisan key:generate

# 3. point the app at your database (edit .env)
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=supermarket_goods
#    DB_USERNAME=root
#    DB_PASSWORD=
#    APP_URL=http://localhost:8000

# 4. create the schema and demo data
php artisan migrate --seed

# 5. expose uploaded images
php artisan storage:link

# 6. build assets
npm run build                   # or: npm run dev
```

Run it:

```bash
php artisan serve               # http://localhost:8000
```

Development with hot reload:

```bash
npm run dev                     # terminal 1
php artisan serve               # terminal 2
```

---

## Demo accounts

Sign in with the **username** (or the email address) and the password `password`.

| Role | Username | Email | Can do |
| --- | --- | --- | --- |
| Administrator | `admin` | `admin@supermarket.test` | Everything, including users and roles |
| Inventory Manager | `manager` | `manager@supermarket.test` | Goods, classifications, suppliers, stock, orders |
| Staff | `staff` | `staff@supermarket.test` | Read catalogue, view stock, view goods |

Seed data: **10 departments → 42 sub-aisles → 109 goods items** across 7 suppliers,
with stock movement history and a real photograph for every single item. Prices are
whole kyat, ~27 items are on promotion, 15 are new arrivals, 4 are still *coming soon*,
6 are expired and 47 are close to their expiry date — the same states the shop enforces.

---

## Product images

Every seeded product ships with a real photograph from **Wikimedia Commons**, credited
on the product page ("Photo: author · licence · via Wikimedia Commons").

| File | Purpose |
| --- | --- |
| `resources/data/product-images.json` | slug → locked Commons file, source URL, author, licence |
| `storage/app/public/products/{slug}.jpg` | the 800px photographs (109 files, ~15MB) |
| `scripts/product-image-queries.php` | curated search term + hand-picked file titles per slug |
| `scripts/fetch-product-images.php` | downloads the photos and rewrites the manifest |
| `scripts/commons-search.php` | prints raw search results, for picking a title by hand |
| `scripts/image-contact-sheet.php` | builds a labelled contact sheet of every image |

Re-fetch, repair or add images:

```bash
php scripts/fetch-product-images.php                 # fill in anything missing
php scripts/fetch-product-images.php --slug=pineapple  # re-fetch one
php scripts/fetch-product-images.php --unlock        # ignore locked titles, re-search
php scripts/fetch-product-images.php --dry           # only print candidates

# eye-check the whole set after any change
php scripts/image-contact-sheet.php
# then open storage/app/private/image-contact.html
```

Commons' relevance search is good but not trustworthy on its own — for grocery terms it
returns laboratory plates, market scenes, book covers and even photographs of people.
So around 30 slugs in `productImageOverrides()` are pinned to hand-verified file titles,
and everything was reviewed on a contact sheet before being committed.

**Seeding still works offline.** If a `.jpg` is missing, `ProductSeeder` generates an SVG
placeholder (initials on a department-coloured gradient) instead, so `migrate --seed`
never fails and never needs the network.

> The images are Creative Commons / public domain and are only here to make the demo
> look real. Replace them with your own photography before shipping anything.

---

## What is in the box

### Storefront (no login)
* `/catalog` — searchable, filterable grid of goods, under a hero that pairs an
  advertisement with a 5-slide carousel
  * search by name, brand, SKU **or barcode**
  * filter by classification, in-stock, on-promotion, coming soon
  * sort by name, price, newest, most viewed
* `/catalog/new-arrivals` — what landed in the last 30 days
* `/catalog/{classification}` — nested department → aisle → goods
* `/catalog/goods/{product}` — price, stock, dates, SKU, barcode, related items, favourite button
* `/cart` → `/checkout` → `/checkout/{order}` → provider page
* `/favourites` — wishlist, works for guests and merges into the account on sign-in

On a phone the classification bar collapses into a single menu icon that opens the
full department tree; on desktop it is a horizontal bar where **exactly one** entry is
ever highlighted — the department you are in, not "All goods" as well.

### Admin panel (`/admin`, login required)
| Screen | Route | Roles |
| --- | --- | --- |
| Dashboard | `/admin` | all |
| Goods CRUD + filters | `/admin/products` | view: all · edit: manager+ |
| Classification tree CRUD | `/admin/categories` | manager+ |
| Suppliers CRUD | `/admin/suppliers` | manager+ |
| Stock movements + log | `/admin/stock` | view: all · record: manager+ |
| Low stock alerts | `/admin/stock/low` | all |
| Orders | `/admin/orders` | all |
| Users & roles | `/admin/users` | admin |
| Activity log | `/admin/activity` | manager+ |

### Goods classification
Nested, unlimited-depth tree with a materialised path for fast subtree queries:

* `categories.depth` and `categories.path` (`/1/5/9/`) are maintained automatically
* cycle protection: a classification can never be moved inside its own subtree
* deleting a parent promotes its children and unclassifies its goods (nothing is lost)
* each node shows the number of goods in its whole branch
* reorder positions, toggle visibility, and search all have endpoints

### Search
* Goods: `name`, `sku`, `brand`, `description` and a digit-stripped `barcode` match
* a MySQL `FULLTEXT` index is created for future relevance ranking
* classifications: name + slug
* users, suppliers, activity log all searchable
* the search box auto-submits after a 450 ms debounce

### Stock
* Every change writes a `stock_movements` row (in / out / adjustment / return) with
  the resulting balance, reason, reference and the staff member
* balance can never go negative
* each goods item has a `min_stock` reorder threshold; anything at or below it appears
  on the low-stock watchlist and in the sidebar badge
* dashboard shows stock value, cost value, potential margin and 30-day movement ranking

### Freshness — an expired or not-yet-landed batch can never be bought
Each goods item carries a production date, an expiry date and an optional *available from*
date. `Product::isSellable()` is the single rule the whole app obeys, and it is enforced in
five places rather than only in the UI:

| Situation | Result |
| --- | --- |
| Expired batch | no add-to-cart button, `POST /cart` refused, `Expired` badge, "no longer for sale" |
| Expiring in ≤ 30 days | still sold, amber `Expires in Nd` badge, admin filter |
| `available_from` in the future | `Coming soon` badge, no buy button, listed with its shelf date |
| Batch expires *while* it sits in the cart | the cart flags it, checkout is blocked |
| Batch is pushed back *while* it sits in the cart | the cart flags it, checkout is blocked |

Every screen that shows an item describes the batch in the same words, from
`Product::freshness()`:

* **product page** — `Packed 10 Oct 2026` · `Best before 14 Oct 2026` · `4 days shelf life` · `On the shelf 11 Oct 2026`
* **cards, cart, checkout and order lines** — one compact line, `Best before 5 Oct 2026 · 7 days left`
* colour follows the urgency: emerald when fresh, amber when close, rose when expired,
  violet when the batch has not landed yet

### Finding your way around
The storefront keeps no breadcrumb row: the classification bar directly under the header
already says where you are, and exactly one of its entries is highlighted.

The admin panel does have one, on every screen (`Dashboard › Goods › Sourdough Loaf`),
because its sidebar only highlights the current section and gives no parent link. The
leading arrow pops the browser history when there is one and otherwise is a plain link to
the parent, so it still works with JavaScript off. The final crumb is never a link.

### Cart & checkout
* session cart, guest and signed-in alike, with quantity steppers and a free-delivery meter
* totals use the **effective** price, so a promoted item is charged the sale price
* the order stores an immutable snapshot: name, SKU, unit, unit price, line total
* stock only moves when a payment is confirmed, and `Payment::markPaid()` is idempotent so
  a provider delivering the same callback twice cannot double-decrement

### Delivery, quoted by zone
The cart can only quote a range; checkout resolves the exact fee from the township.

| Zone | Fee | ETA |
| --- | --- | --- |
| Inner Yangon | 1,500 Ks | same day |
| Greater Yangon | 2,500 Ks | 1–2 days |
| Other townships | 5,000 Ks | 2–4 days |

Free over 150,000 Ks. Zone and ETA are stored on the order, and the checkout page updates
the fee live as the township changes.

### Payments
KBZPay, Wave Money, AyaPay, UAB Pay, cash on delivery and a **sandbox** simulator.

* `PAYMENTS_DRIVER=sandbox` is the default, so the whole flow is testable with no
  merchant account; the sandbox page signs its own callbacks and rejects forged ones
* a live gateway only appears at the checkout once it is both enabled *and* completely
  configured, so a half-filled account never shows a broken option at the till
* reloading the order page reuses the open payment attempt instead of minting a duplicate
  intent with the provider
* **endpoints and signature canonicalisation must be checked against each provider's
  current merchant docs before going live** — the sandbox path is what is tested here

### Audit trail
`activity_logs` records create / update / delete / stock_in / stock_out / login with the
acting user, subject and description.

### Dashboard

`/admin` is built around one question first — *are the shelves healthy?*

1. **Shelf health banner** — a single headline, a three-segment bar (healthy / running low /
   out of stock) with live percentages, and an "Action needed" shortcut to the reorder list.
2. **KPI strip** — four tiles, one number and one meaning each, animated count-up.
3. **Restock queue** — the actionable core: sorted empty-shelf-first, with a stock-vs-threshold
   bar, the suggested order quantity and a one-click Restock button.
4. **Goods movement** — 30-day stacked column chart of goods in vs goods out.
5. **Where your money sits** — stock value share per department, top 8 plus a tail row.
6. **Fastest movers**, **What changed** (login noise removed) and **On shift**.

Live behaviour: KPI counters animate, every bar grows from zero on load, a ticking
"updated Ns ago" indicator, and an optional 60-second auto-refresh toggle.
All of it respects `prefers-reduced-motion`.

### Homepage advertising
The banner above the catalogue grid is two columns — the carousel on the left, the
advertising copy on the right (stacked on a phone, where the copy is set larger).

* the wording lives in `config/shop.php` (`shop.hero.*`), so a campaign can be changed in
  `.env` with `SHOP_HERO_HEADLINE`, `SHOP_HERO_SUBLINE` and `SHOP_HERO_CTA`
* the **product slides are chosen automatically**: promotions and new arrivals that can
  actually be bought and have a real photograph, so nothing unbuyable is ever advertised
* `PROMO_VIDEO` adds an advertisement video as the first slide — drop an `.mp4`/`.webm` in
  `storage/app/public/promo/` and point the variable at it. A configured path with no file
  on disk is ignored, so a missing upload breaks nothing
* autoplay, arrows, dots, arrow keys and swipe all work; autoplay is skipped for anyone who
  asks for reduced motion, pauses on hover/focus and while the tab is hidden

---

## Roles

| Capability | Staff | Manager | Admin |
| --- | :---: | :---: | :---: |
| View dashboard, goods list, stock log | ✅ | ✅ | ✅ |
| Create / edit / delete goods | ❌ | ✅ | ✅ |
| Manage classifications, suppliers | ❌ | ✅ | ✅ |
| Record stock movements | ❌ | ✅ | ✅ |
| View orders | ✅ | ✅ | ✅ |
| Update order status | ❌ | ✅ | ✅ |
| Manage users and roles | ❌ | ❌ | ✅ |

Enforced twice: the `role:admin,manager` middleware on routes **and** `authorize()` in
every form request.

---

## Project structure

```
app/
  Cart/            CartService (session cart), FavouriteService (guest + account wishlist)
  Enums/           OrderStatus, PaymentGateway, PaymentStatus, Role, StockMovementType
  Http/
    Controllers/
      Admin/        Dashboard, Product, Category, Stock, Supplier, User, Order, Activity
      Auth/         AuthenticatedSessionController
      CartController, CheckoutController, PaymentController, FavouriteController
      CatalogController
    Middleware/     EnsureUserHasRole, EnsureUserIsActive
    Requests/Admin/ Form-request validation per resource
  Models/          Category, Product, Favourite, Order, OrderItem, Payment,
                   StockMovement, Supplier, ActivityLog, User
  Payments/        PaymentManager + Gateways (KbzPay, AyaPay, WaveMoney, UabPay,
                   Cash, Sandbox) behind one PaymentGateway contract
  Support/         Money (whole kyat), Delivery (zones)
  Services/        CategoryTreeService
database/
  factories/       User, Category, Product, Supplier
  migrations/      users profile, categories, products, dates, sale prices, new flag,
                   orders + delivery zone, favourites, availability
  seeders/         User, Category, Product (+ photos and dates), Supplier, Favourite
resources/
  css/app.css      design tokens + component layer
  js/app.js        drawer, dropdowns, tree toggles, debounced search, shelf life,
                   delivery quote, quantity steppers
  views/
    admin/         dashboard, products, categories, stock, suppliers, users, orders, activity
    catalog/       index, new-arrivals, category, product
    cart/          index
    checkout/      create, show, sandbox
    favourites/    index
    auth/          login, register
    components/    layouts, icon set, price, freshness, breadcrumbs,
                   favourite button, cards, badges, form field
routes/web.php
scripts/           smoke.php, shop-flow.cjs, shot.cjs, shot-one.cjs, lint-views.php,
                   product image tooling
tests/Feature/     128 feature tests
resources/data/    product image manifest (attribution)
DESIGN.md          Figma-rebuildable design system spec
```

---

## Testing & tooling

```bash
php artisan test                 # 128 feature tests
vendor\bin\pint                  # code style (Laravel preset)

php scripts/lint-views.php       # compiles every Blade view and php -l's the result
php scripts/smoke.php            # 52 end-to-end HTTP checks against a running server
php scripts/smoke.php --write    # also runs the checks that create records
node scripts/shop-flow.cjs       # headless cart -> checkout -> sandbox payment, with screenshots
node scripts/shot-one.cjs <url> <name> [width] [--click=<selector>]
```

`scripts/smoke.php` is read-only by default: it signs in, walks every public and admin
page, and drives the full cart → checkout → sandbox payment flow, then empties the cart
and clears the wishlist again. Pass `--write` for the checks that create goods,
classifications, suppliers and users. Start the server on port 8000 first.

`scripts/shop-flow.cjs`, `shot.cjs` and `measure.cjs` drive headless Chrome over the
DevTools protocol to capture screenshots and detect horizontal-overflow at any viewport.

---

## Design system

See **[DESIGN.md](DESIGN.md)** — colour variables, type styles, elevation, the component
library, screen layouts, breakpoints, motion rules and accessibility notes, all written so
the interface can be recreated in Figma without reading the code.

---

## API-ish endpoints (JSON)

Two endpoints return JSON for future integrations:

* `POST /admin/categories/{category}/toggle` → `{ ok, is_active, message }`
* `POST /admin/categories/reorder` → accepts `{ order: [{ id, position }] }`

---

## Notes & limitations

* Single-tenant: one store, no branch/location dimension.
* Stock is not reserved at checkout, so two shoppers racing for the last unit can both
  reach the till; the second order settles with the balance clamped at zero.
* Barcode lookup matches the numeric part of the term, so `481000021732` finds
  `48 1000021732`; there is no scanner hardware integration.
* Sessions and cache use the database driver, so MySQL must be running to serve pages.
* Live payment endpoints and signatures are unverified — confirm them against each
  provider's merchant documentation before enabling `PAYMENTS_DRIVER=live`.
