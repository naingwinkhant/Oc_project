# Goods Hub — Design System & Figma Rebuild Spec

Everything below is expressed in plain Figma concepts (variables, styles, auto-layout)
so the interface can be rebuilt in Figma 1:1 from the code, or handed to a designer.

---

## 1. Foundations

### 1.1 Colour — Figma Variables (Modes: `Light`)

Create a **Variables** collection named `color` with these values. They are authored as
`oklch` in `resources/css/app.css`; the hex values below are the sRGB equivalents.

#### Brand (primary — fresh/supermarket green)

| Variable | Value | Use |
| --- | --- | --- |
| `brand/50` | `#F0FDF6` | tinted backgrounds, hover fills |
| `brand/100` | `#D9F9EA` | icon chips, soft badges |
| `brand/200` | `#B5EFD6` | borders on tinted surfaces |
| `brand/500` | `#10B981` | progress bars, data-viz accent |
| `brand/600` | `#0F9D6E` | primary buttons, active nav |
| `brand/700` | `#0C7C58` | hover on primary, links |
| `brand/900` | `#134E3C` | text on tinted surfaces |

#### Ink (neutral)

| Variable | Value | Use |
| --- | --- | --- |
| `ink/50` | `#F7F8FA` | table header, subtle fills |
| `ink/100` | `#EFF1F4` | page background, hover rows |
| `ink/200` | `#DFE3E8` | all borders, dividers |
| `ink/300` | `#C6CCD4` | input borders, scrollbar |
| `ink/400` | `#8E97A3` | placeholder, meta text |
| `ink/500` | `#6E7883` | secondary text, table heads |
| `ink/700` | `#3B4550` | form labels, body-strong |
| `ink/800` | `#252D36` | **body text** |
| `ink/900` | `#10161C` | headings, primary text |

#### Semantic status

| Token | Background | Text | Border ring | Meaning |
| --- | --- | --- | --- | --- |
| `success` | `#ECFDF5` | `#047857` | `#10B981` 20% | in stock, saved, active user |
| `warning` | `#FFFBEB` | `#B45309` | `#F59E0B` 20% | low stock, needs attention |
| `danger` | `#FFF1F2` | `#BE123C` | `#F43F5E` 20% | out of stock, delete, error |
| `info` | `#F0F9FF` | `#0369A1` | `#0EA5E9` 20% | stock in, returns, info banner |
| `neutral` | `#F1F3F6` | `#3B4550` | `#64748B` 20% | hidden / archived items |

**Rule:** badges are always a 100-level tint background + 700-level text + a 20%-opacity
1px ring of the 500/600 colour. Never a solid fill.

### 1.2 Typography

Font family: **Plus Jakarta Sans** (fallback `Segoe UI`, system sans).
Figma: create a Text Style per row.

| Style | Size / Line | Weight | Tracking | Used by |
| --- | --- | --- | --- | --- |
| `display/mobile` | 20 / 28 | Bold 700 | -0.2% | hero title < 640px |
| `display/desktop` | 30 / 36 | Bold 700 | -0.3% | hero title ≥ 640px |
| `title/lg` | 18 / 24 | Bold 700 | -0.2% | page heading, product name |
| `title/md` | 16 / 22 | Bold 700 | -0.1% | card titles |
| `body/lg` | 15 / 24 | SemiBold 600 | 0 | buttons, nav items, table names |
| `body/md` | 14 / 20 | Regular 400 | 0 | inputs, body copy, table cells |
| `body/sm` | 13 / 20 | Medium 500 | 0 | secondary copy, table meta |
| `label` | 12 / 16 | SemiBold 600 | +2% UPPERCASE | stat labels, table heads |
| `meta/mono` | 11 / 16 | Medium 500 | 0 | SKU, slug, barcode (mono) |

All **numerals** in tables, prices and counters use `font-feature-settings: 'tnum'`
in Figma: enable *Tabular Numbers* in the type panel.

### 1.3 Spacing, radius, elevation

* Spacing scale: `4 · 8 · 12 · 16 · 20 · 24 · 32 · 40 · 48 · 64` (4px base).
* Radius: `sm 6` (badges, chips) · `md 8` (inputs, buttons, nav) · `lg 12` (cards) · `xl 16` (hero, logo tile) · `full` (pills, avatars).
* Elevation (Figma *Effect styles*):

| Name | Y / Blur / Spread | Colour |
| --- | --- | --- |
| `elevation/card` | 1 / 2 / 0 | `#141E2D` 6% |
| `elevation/raise` | 4 / 6 / -1 | `#141E2D` 7% |
| `elevation/pop` | 10 / 15 / -3 | `#141E2D` 8% |
| `elevation/none` | — | borders only |

* Border colour is always `ink/200` at 80–100% opacity. Cards get a border **and** `elevation/card`.

### 1.4 Grid & breakpoints

| Breakpoint | Tailwind | Figma frame width | Admin shell | Catalogue grid |
| --- | --- | --- | --- | --- |
| Mobile | `< 640` | 390 | drawer sidebar, 1-col cards | 2 columns |
| Tablet | `≥ 640` | 768 | drawer sidebar | auto-fill ≥ 224px |
| Desktop | `≥ 1024` | 1440 | fixed 288px sidebar | 4 columns |
| Wide | `≥ 1280` | 1920 | fixed sidebar | 4–5 columns |

Catalogue grid rule: `repeat(auto-fill, minmax(14rem, 1fr))`, gap `12` (mobile) / `16` (≥640).

---

## 2. Component library

Rebuild these as Figma **components** (with variants) before composing screens.

### 2.1 `Button`

Auto-layout: horizontal, gap 8, padding `12/20` (lg) · `10/16` (sm), radius 8.
Variants:

| Variant | Background | Text | Border | Hover |
| --- | --- | --- | --- | --- |
| `primary` | `brand/600` | white | — | `brand/700` |
| `secondary` | white | `ink/700` | `ink/300` 1px | `ink/50` |
| `ghost` | transparent | `ink/600` | — | `ink/100` |
| `soft` | `brand/50` | `brand/700` | `brand/600` 15% | `brand/100` |
| `danger` | `#E11D48` | white | — | `#BE123C` |

Sizes: `sm` (32px tall) · `md` (40px) · `lg` (48px, full-width in forms).
All buttons: `disabled` = 50% opacity, no pointer.

### 2.2 `Input`

Radius 8, height 40 (32 for `sm`), padding `12/16`, `ink/300` 1px border, `elevation/card`.
* **Focus**: border `brand/500` + outer ring `brand/500` 25% at 2px.
* **Error**: border `#F87171`, help text below at 12px `danger`.
* **Select**: custom 16px chevron, right padding 36, background-position right 10px center.
* **Checkbox**: 16px, radius 4, checked fill `brand/600`.
* **Search input**: icon `16px` absolutely positioned 12px from left, `text/placeholder` = `ink/400`.

### 2.3 `Badge`

Radius full, padding `2/8`, 11px SemiBold, `whitespace-nowrap`, 1px inset ring.
Semantic variants per §1.1. Use for: stock status, roles, supplier status, featured flag.

### 2.4 `Card`

Radius 12, white fill, 1px `ink/200` @ 80%, `elevation/card`.
Sub-slots: `header` (padding 12/16, bottom border), `body` (padding 16/20), `footer` (top border).

### 2.5 `Stat card`

Grid 2 columns (icon right). Content: `label` (uppercase 12) → `value` (28px Bold, tabular)
→ footer row `trend` + `hint`. Icon tile 40×40, radius 8, tinted bg + 1px ring.
Tone variants: brand · emerald · amber · rose · sky · violet.

Used on the dashboard as the **KPI tile**, where the whole card is a link: hover lifts it
2px and swaps to `elevation/raise`, and the icon tile scales to 105%.

### 2.5b `Segmented health bar`

Three linked segments in a 12px rounded track, each an `<a>` with a `title` tooltip
(`label: count (percent%)`). Widths are true percentages with a 1.5% floor. Segment
colours: healthy `emerald/500`, low `amber/400`, out `rose/500`; each darkens on hover.
Use this pattern only for a single whole split into a few parts — it reads as one number.

### 2.6 `Product card`

Vertical, padding 14, aspect-ratio `4:3` image on top.
* Badges overlay: top-left (`Hidden`, `Featured`).
* Body: category badge → name (2-line clamp) → SKU (mono 11px `ink/400`) → footer row.
* Footer: price (18px Bold) + `/ unit` (11px `ink/400`), right-aligned stock badge.
* Hover: translateY −2px + `elevation/pop`; image `scale(105%)` over 500ms.

### 2.7 `Nav item`

Radius 8, padding `10/12`, gap 12, icon 18px.
States: default (`ink/600` text, transparent) · hover (`ink/100` bg) · **active**
(`brand/50` bg, `brand/700` text, semibold) · optional trailing count pill.

### 2.8 `Empty state`

Centered, icon tile 56×56 radius 16 `ink/100` + `ink/400` icon, title (`title/md`),
description (`body/sm`, max 320px), optional action button.

### 2.9 `Toast / flash`

Radius 8, padding `12/16`, semantic tint + ring, icon left, dismissable right.
Auto-dismiss after 4.5s, slide-up 250ms.

### 2.10 `Table`

Header: `ink/50` bg, `label` style, sticky optional. Row height 56.
`hover` row = `brand/50` @ 40%. Cell padding `12/16` (`12/12` on mobile).
On `< 1024` the table scrolls horizontally inside a `12px` radius container — never
collapse into a card list; keep column priority: goods → price → stock → status → actions.

### 2.11 `Pagination`

Prev / page numbers / next, 32px square buttons, current page = `primary`.
Above it: “Showing 1 to 15 of 109” in `meta`.

### 2.12 `Classification tree row`

Left: 24×24 chevron button (rotate 90° when expanded) → 32×32 icon tile
(`tag` for depth 0 in `brand/100`, `folder` for deeper in `ink/100`) → name + slug.
Right: item-count pill (amber when 0) + hover action cluster
(view / add sub / edit / delete). Children indent 12px with a 1px `ink/200` left rail.

### 2.13 `Icon set

All icons are **inline SVG, 24×24 viewBox, 1.6 stroke, round caps** (`resources/views/components/icon.blade.php`).
In Figma, import as a single icon component set with a *stroke-only* style.
Names used: `dashboard, box, layers, tag, users, user, truck, clipboard, search, plus,
minus, pencil, trash, check, x, chevron-down/up/left/right, menu, bell, logout,
arrow-up/down/right/left, filter, alert, info, store, cart, barcode, settings, eye, chart,
calendar, clock, home, refresh, sliders, download, upload, sparkles, image, mail, phone,
map-pin, inbox, grid, list, sort, shield, square, grip, tag-check, scale, folder`.

---

## 3. Screen specs

### 3.1 Admin shell (`/admin/*`)

```
┌──────────────┬───────────────────────────────────────────────┐
│ SIDEBAR 288  │ TOPBAR 64 (sticky, blur 12px, white/85)      │
│              │ [☰ mobile] Heading+desc │ Search │ [Actions]  │
│ brand tile   ├───────────────────────────────────────────────┤
│ ── Overview  │                                               │
│    Dashboard │  PAGE CONTENT  (padding 16 / 24)             │
│ ── Catalogue │                                               │
│    Goods     │                                               │
│    Classif.  │                                               │
│    Suppliers │                                               │
│ ── Operations│                                               │
│    Stock mv. │                                               │
│    Low stock │                                               │
│ ── Admin     │                                               │
│    Users     │                                               │
│    Activity  │                                               │
│              │                                               │
│ ─────────────│                                               │
│ Public cata. │                                               │
│ [avatar card]│                                               │
└──────────────┴───────────────────────────────────────────────┘
```

* Sidebar: white, right border `ink/200`, groups separated by 24px, group label = `label` style.
* Low-stock nav item shows a **count pill** (rose) when items need restock.
* User card pinned bottom: 36px avatar (initials in `brand/600` circle), name, role, sign-out icon button.
* Mobile: sidebar becomes a left drawer 288px, overlay `ink/950`/50% + 4px blur, body scroll locked.
* Topbar search hidden `< 768`; page Actions slot top-right, icon-only on mobile.

### 3.2 Dashboard

The dashboard answers one question first — *are the shelves healthy?* — then drills down.
Read it top to bottom; nothing below the first screen is required to run the store.

**Row 1 — Shelf health (full width).** The single headline answer.
* Icon tile + `section-title` "Shelf health" + `title/lg` headline
  ("Every shelf is fully stocked" / "Some items need reordering" / "Some shelves are empty — reorder today")
* One supporting line: `NN%` of `NNN` goods items are above their reorder threshold
* **Segmented bar**, 12px tall, three linked segments in order green → amber → rose.
  Each segment is an `<a>`; widths are the true percentages, min 1.5% so nothing vanishes.
* Legend underneath: dot + label + count + `(percent)` per segment.
* Right side (≥1024px): "Action needed" panel — count of low + out items, and a rose
  arrow button linking to the low-stock list. Turns the whole row into the primary CTA.

**Row 2 — KPI strip.** Four tiles, `2 cols mobile → 4 cols ≥1280`. Each links somewhere
useful. One number, one meaning, one supporting line:

| Tile | Value | Supporting line |
| --- | --- | --- |
| Goods items | count | `NN visible · NN hidden` |
| Stock value | `₱` + 2dp | `NN% potential margin` |
| Classifications | count + `dept` | `NN aisles · NN unclassified` |
| Suppliers | count | `NN active` |

Numbers **count up** from 0 on load (900ms cubic ease-out, `Intl.NumberFormat`).

**Row 3 — Restock queue (2/3) + Goods movement (1/3).**
*Restock queue* — the actionable core. Sorted empty-shelf-first, then scarcest.
Row: `40px` thumb · name + `SKU · aisle` · **fixed 176px progress track** with
`N left` (red/amber) above and `min N` right-aligned · suggested order `+N unit` (≥1024px) ·
Restock button (rose when empty, soft green when low). Full row is not a link — only the
button and the name are. Collapses on mobile to thumb + name + icon button.
*Goods movement* — 30-day stacked column chart, 160px tall, 3px gap between days.
Green segment = goods in (bottom), rose = goods out (top). Days with no movement render a
3px `ink/200` dash so the time axis stays continuous. Legend row above shows In / Out /
net badge; footer states how many of the 30 days had activity.

**Row 4 — Where your money sits (2/3) + What changed / On shift (1/3).**
*Where your money sits* — top 8 departments by stock value plus a greyed
**Other departments** row for the tail. Each row: name + item count (left),
`₱value` + `(share%)` (right), 8px bar scaled `share × 3` so the top department
fills about two thirds of the track.
*Fastest movers* — ranked `1..5`, each with a share bar, percentage and unit.
*What changed* — action badge + description + `user · time`, **login events excluded**.
*On shift* — 4 avatars with role and `last seen`.

**Layout rule:** every grid uses `items-start`, so a short card never stretches and
leaves a void beneath it. Content is distributed so the left and right columns finish
within roughly one card of each other.

### 3.3 Dynamic behaviour

| Element | Behaviour |
| --- | --- |
| KPI numbers | count up 0 → value over 900ms, `Intl.NumberFormat`, integer or PHP currency |
| Health bar + progress bars + share bars | grow from `width: 0` to `--w` over 850ms, staggered 35ms per bar, max 700ms total delay |
| Live dot | 2px `brand/500` with a `ping` halo |
| Timestamp | ticks `just now` → `Ns ago` → `Nm ago` every 5s |
| Auto-refresh | button toggles a 60s `location.reload()`, label swaps to `Auto-refresh 60s`, button turns `soft` when armed |
| Reduced motion | counters render their final value immediately, bars skip the animation |

### 3.3 Goods (`/admin/products`)

Filter card: search (2 cols) + classification + status + sort + Apply/Reset.
Table columns: Goods (thumb 40px + name + SKU/brand) · Classification (≥1024) ·
Price (+cost sub-line) · Stock · Status (≥640) · Actions.
Status filter values: active · hidden · low · out · featured.

### 3.4 Goods form (`/admin/products/create|edit`)

2/3 column split:
* **Left** — “Item details” (name full width; sku, barcode, brand, classification;
  description textarea) and “Pricing & stock” (price, cost, unit, weight, stock, min stock).
* **Right** — Photo card (4:3 preview + file button + client-side preview),
  Visibility card (visible / featured toggles, slug when editing),
  sticky submit + cancel.
Submit label: `Add to catalogue` / `Save changes`.

### 3.5 Classifications (`/admin/categories`)

4 stat cards → 2/3 tree + 1/3 side (search + “tips for a clean tree”).
Tree rows expand/collapse inline (no page reload). Row actions appear on hover (desktop),
always visible (mobile).

### 3.6 Stock movements (`/admin/stock`)

3 stat cards → left: filters + movement table; right: sticky “Record movement” card
(goods select showing current stock, 4 type radios, quantity, reason, reference).

### 3.7 Users (`/admin/users`)

4 stat cards → filters → table (avatar + name + email, role badge, last sign-in, status, actions)
→ “What each role can do” table describing the three roles.

### 3.8 Public catalogue (`/catalog`)

* Header 64: brand · search (flex) · Sign in / Dashboard · mobile category button.
* Desktop sub-nav: horizontal scrollable pills of top-level departments.
* Hero: `brand/700`, radius 12, title + count copy + “Staff sign in” ghost button.
* Mobile: `<details>` disclosure “Classifications & filters” with pill row + 2 checkboxes.
* Body: 288px sticky sidebar (classification tree + refine) + product grid.
* Breadcrumbs + child-department chip rail on category pages.
* Product page: 4:3 image, detail card with price/stock/SKU/barcode, specifications grid,
  recent stock activity, related items.

---

## 4. Interaction & motion

| Interaction | Spec |
| --- | --- |
| Card / row hover | 150ms `ease-out`, background or `translateY(-2px)` + `elevation/pop` |
| Button press | 100ms, background darkens one step |
| Sidebar drawer | 200ms `cubic-bezier(.4,0,.2,1)` slide, overlay fades 150ms |
| Tree expand | chevron rotates 90° over 150ms; branch shows/hides |
| Flash toast | enters 320ms `cubic-bezier(.22,1,.36,1)` slide-up, exits 250ms |
| Product image hover | `scale(1.05)` over 500ms |
| `.bar-animate` | `width: 0` → `var(--w)` over 850ms `cubic-bezier(.22,1,.36,1)`, staggered 35ms/bar, delay capped at 700ms |
| KPI counters | 0 → value over 900ms with cubic ease-out via `requestAnimationFrame` |
| Live dot | continuous `ping` halo, 1s cycle |
| Search field | debounced auto-submit at 450ms |
| Focus ring | 2px `brand/600` at 2px offset, all interactive elements |
| Reduced motion | `@media (prefers-reduced-motion: reduce)` disables all of the above; counters jump to their final value |

---

## 5. Accessibility

* Body text ≥ 12px; interactive hit area ≥ 32×32 (44×44 recommended on mobile).
* Colour is never the only signal: stock state always pairs colour + text label.
* Every input has a `<label>`; errors render text + `role="alert"` via the help row.
* Tables use real `<th scope>`; icon-only buttons carry `aria-label` / `title`.
* Drawer and dropdowns close on `Escape` and on outside click.
* Contrast: body `ink/800` on white ≈ 12:1; muted `ink/500` ≈ 5.6:1; badges ≥ 4.5:1.

---

## 6. Implementation map (code → Figma)

| Concern | File |
| --- | --- |
| Design tokens, components layer | `resources/css/app.css` |
| Icon set | `resources/views/components/icon.blade.php` |
| Admin shell | `resources/views/components/layouts/app.blade.php` |
| Public shell | `resources/views/components/layouts/public.blade.php` |
| Auth shell | `resources/views/components/layouts/guest.blade.php` |
| Behaviour (drawer, dropdowns, search) | `resources/js/app.js` |
| Screens | `resources/views/admin/**`, `resources/views/catalog/**` |
