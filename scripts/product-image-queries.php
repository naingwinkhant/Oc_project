<?php

/**
 * Curated Wikimedia Commons search term per product slug.
 *
 * Generic product names search badly — "Onions (Red)" returns a market scene and
 * "Pineapple" returns a book cover — so every slug gets an explicit term naming the
 * species or the specific product form we want a photo of.
 *
 * Run from scripts/fetch-product-images.php. Editing a value and re-running with
 * --slug=<name> --unlock replaces just that image.
 */

/**
 * Hand-picked Commons file titles, verified by eye against a contact sheet.
 *
 * Commons' relevance search returns laboratory plates, market scenes and book
 * covers for a startling number of grocery terms, so these are pinned by hand.
 * The fetcher prefers an override, then a title already locked in
 * resources/data/product-images.json, and only then falls back to search.
 */
function productImageOverrides(): array
{
    return [
        'cavendish-bananas' => 'File:Cavendish banana from Maracaibo.jpg',
        'royal-gala-apples' => 'File:Liat Portal for Foodie Disorder - Red Apple (Whole Fruit).jpg',
        'potatoes-lady-finger' => 'File:Potato tubers in pail.jpg',
        'tomatoes-roma' => 'File:Raw Tomatoes.jpg',
        'onions-red' => 'File:Red Onion on White.JPG',
        'kalamata-olives' => 'File:Egyptian Olives.jpg',
        'coriander-leaves' => 'File:A scene of Coriander leaves.JPG',
        'beef-brisket' => 'File:Brisket topped with fat - Decmeber 2023 - Sarah Stierch.jpg',
        'beef-ribeye' => 'File:Grillfleisch Rindersteak Ribeye (26152657274).jpg',
        'pork-liempo' => 'File:HK food ingredient red meat frozen pork chop raw butt steak October 2021 SS2 006.jpg',
        'pork-belly' => 'File:HK food ingredient red meat frozen pork chop raw butt steak October 2021 SS2 011.jpg',
        'chicken-breast-fillets' => 'File:Raw chicken slices.jpg',
        'milkfish-bangus' => 'File:Susu at Giant Hypermarket Kota Damansara 20230203 105744.jpg',
        'tuna-loin' => 'File:Thunnus obesus (bigeye tuna).jpg',
        'squid-pusit' => 'File:20190415 Yehliu fish market squid-2.jpg',
        'cheddar-cheese-block' => 'File:Somerset-Cheddar.jpg',
        'sinandomeng-rice-5kg' => 'File:WhiteRice.jpg',
        'instant-coffee-100g' => 'File:Instant coffee.jpg',
        'potato-crisps-case' => 'File:Potato chips 2.jpg',
        'bbq-flakes-case' => 'File:Shing-a-ling.jpg',
        'corn-chips-case' => 'File:Corn chips.jpg',
        'bleach-1l' => 'File:HK Kao Bleaches 2.JPG',
        'facial-tissue-case' => 'File:A tissue box.jpg',
        'fabric-softener-900ml' => 'File:Globus Saarbrücken, fabric softener pic1.JPG',
        'garbage-bags-30s' => 'File:Black garbage bag.jpg',
        'baby-lotion-200ml' => 'File:Baby Lotions, Soaps, and Diaper Creams at Kroger.JPG',
        'mixed-vegetables-500g' => 'File:Frozen Vegetables.jpg',

        // Second pass: the relevance search returned people, cooked dishes or shop
        // shelves for these, so they were replaced after eyeballing the contact sheet.
        'baby-diapers-m-32s' => 'File:Pampers on the shelves.jpg',
        'baby-lotion-200ml' => "File:Johnson's Baby products at Watsons at Jincheng, Kinmen-20191216.jpg",
        'baguette' => 'File:La Brea Bakery French Baguette (27072058008).jpg',
        'chinese-pechay' => 'File:Young Bok Choy in garden.jpg',
        'cheese-danish' => 'File:Glazed apple Danish.jpg',
        'facial-tissue-case' => 'File:Chrome Kleenex Tissues Box - Matane, Quebec - July 2022.jpg',
        'floor-cleaner-1l' => 'File:Spray cleaner.jpg',
        'food-storage-container-3l' => 'File:Tupperware-PP.jpg',
        'ground-coffee-250g' => 'File:Coffee powdered.jpg',
        'instant-noodles-case' => 'File:Cup Noodles (cropped).jpg',
        'lemon-soda-15l' => 'File:R Whites lemonade (2).JPG',
        'tea-bags-25s' => 'File:Teabag with green tag.jpg',
        'beef-patty-case' => 'File:Hamburger meat patty patties lettuce tomatoes buns.jpg',
        'red-wine-750ml' => 'File:Paso Robles red blend unique wine bottle.jpg',
    ];
}

function productImageQueries(): array
{
    return [
        // Fresh produce
        'cavendish-bananas' => 'Cavendish banana fruit bunch Musa',
        'royal-gala-apples' => 'Royal Gala apple whole fruit',
        'mangoes-carabao' => 'Carabao mango fruit Philippines',
        'pineapple' => 'pineapple fruit Ananas whole sliced',
        'watermelon' => 'watermelon fruit whole sliced red',
        'valencia-oranges' => 'orange fruit whole Citrus sinensis',
        'potatoes-lady-finger' => 'fingerling potato tubers',
        'tomatoes-roma' => 'Roma tomato fruit red',
        'carrots' => 'carrot roots bunch Daucus',
        'onions-red' => 'red onion bulbs whole Allium cepa',
        'bitter-melon' => 'bitter melon fruit Momordica charantia',
        'chinese-pechay' => 'bok choy pak choi vegetable',
        'romaine-lettuce' => 'romaine lettuce head vegetable',
        'iceberg-lettuce' => 'iceberg lettuce head vegetable',
        'fresh-spinach' => 'spinach leaves bunch green',
        'kalamata-olives' => 'kalamata olives fruit',
        'fresh-basil' => 'basil leaves Ocimum basilicum green',
        'coriander-leaves' => 'coriander cilantro leaves bunch',

        // Meat & seafood
        'beef-brisket' => 'raw beef brisket meat',
        'ground-beef-pork-free' => 'minced ground beef meat',
        'beef-ribeye' => 'raw beef ribeye steak meat',
        'pork-liempo' => 'raw pork belly slab meat',
        'pork-belly' => 'pork belly raw slab',
        'longganisa' => 'longganisa Filipino sausage',
        'whole-chicken' => 'raw whole chicken meat',
        'chicken-breast-fillets' => 'raw chicken breast fillet',
        'chicken-wings' => 'raw chicken wings',
        'prawns-medium' => 'raw prawns shrimp',
        'milkfish-bangus' => 'milkfish Chanos whole fish',
        'tuna-loin' => 'tuna fish whole fresh',
        'squid-pusit' => 'squid Loligo whole raw',

        // Dairy & eggs
        'fresh-whole-milk-1l' => 'milk bottle glass white',
        'pasteurized-milk-1l' => 'milk carton package',
        'chocolate-milk-1l' => 'chocolate milk glass',
        'cheddar-cheese-block' => 'cheddar cheese block wedge',
        'mozzarella-whole' => 'mozzarella cheese ball',
        'processed-cheese-singles' => 'processed cheese slices',
        'chicken-eggs-large' => 'chicken eggs brown white',
        'duck-eggs' => 'duck eggs',

        // Bakery
        'sourdough-loaf' => 'sourdough bread loaf',
        'sandwich-loaf' => 'white sandwich bread loaf sliced',
        'baguette' => 'baguette bread loaf French',
        'whole-wheat-bread' => 'whole wheat bread loaf brown',
        'butter-croissant' => 'butter croissant pastry',
        'ensaymada' => 'ensaymada bread',
        'cheese-danish' => 'cheese danish pastry',

        // Pantry
        'sinandomeng-rice-5kg' => 'white rice grains sack',
        'garlic-fryer-rice-5kg' => 'garlic fried rice plate',
        'oatmeal-groats-1kg' => 'oat flakes oatmeal',
        'corned-tuna-flakes' => 'tuna flakes dried',
        'tomato-paste' => 'tomato paste can',
        'tuna-in-oil-185g' => 'canned tuna tin',
        'spaghetti-penne-500g' => 'penne pasta dry',
        'instant-noodles-case' => 'instant noodles packet',
        'corned-beef-150g' => 'corned beef tin',
        'coconut-oil-1l' => 'coconut oil bottle',
        'olive-oil-500ml' => 'olive oil bottle',
        'soy-sauce-500ml' => 'soy sauce bottle',
        'black-pepper-whole' => 'black peppercorns whole',
        'iodized-salt-1kg' => 'salt pile iodised',
        'garlic-powder' => 'garlic powder bowl',

        // Beverages
        'purified-water-500ml-case' => 'bottled water plastic bottle',
        'mineral-water-15l' => 'mineral water bottle plastic',
        'cola-15l' => 'cola bottle soft drink',
        'lemon-soda-15l' => 'lemonade soft drink glass',
        'energy-drink-250ml' => 'energy drink can',
        'orange-juice-1l' => 'orange juice glass bottle',
        'mango-juice-1l' => 'mango juice glass',
        'instant-coffee-100g' => 'instant coffee jar granules',
        'ground-coffee-250g' => 'ground coffee beans',
        'tea-bags-25s' => 'tea bags box',
        'pale-pilsen-320ml-case' => 'beer bottles pack',
        'red-wine-750ml' => 'red wine bottle glass',

        // Snacks
        'potato-crisps-case' => 'potato chips bag snack',
        'bbq-flakes-case' => 'corned beef snack strips BBQ flakes',
        'corn-chips-case' => 'corn chips snack bag',
        'milk-chocolate-bar' => 'milk chocolate bar',
        'dark-chocolate-70' => 'dark chocolate bar',
        'sandwich-biscuits-pack' => 'sandwich cookies Oreo',
        'cream-filled-cookies' => 'cream filled cookies Oreo',
        'cashew-nuts-250g' => 'cashew nuts',
        'dried-mangoes-200g' => 'dried mango slices',

        // Household
        'dishwashing-liquid-250ml' => 'dish soap liquid bottle',
        'floor-cleaner-1l' => 'floor cleaner bottle',
        'bleach-1l' => 'bleach bottle household',
        'toilet-paper-10-rolls' => 'toilet paper roll white',
        'facial-tissue-case' => 'facial tissue box',
        'powder-detergent-1kg' => 'laundry detergent powder',
        'fabric-softener-900ml' => 'fabric softener bottle',
        'frying-pan-24cm' => 'frying pan skillet',
        'stainless-steel-pot-20cm' => 'stainless steel cooking pot',
        'garbage-bags-30s' => 'trash bags roll',
        'food-storage-container-3l' => 'plastic food storage container',

        // Personal care
        'bath-shower-soap-90g' => 'bar soap',
        'body-lotion-250ml' => 'body lotion bottle',
        'shampoo-350ml' => 'shampoo bottle',
        'conditioner-350ml' => 'hair conditioner bottle',
        'toothpaste-150g' => 'toothpaste tube',
        'toothbrush-2s' => 'toothbrush',
        'baby-diapers-m-32s' => 'baby diaper',
        'baby-lotion-200ml' => 'baby lotion bottle',

        // Frozen
        'mixed-vegetables-500g' => 'frozen mixed vegetables bag',
        'green-peas-1kg' => 'green peas',
        'chicken-nuggets-250g' => 'chicken nuggets',
        'beef-patty-case' => 'hamburger patty beef',
        'vanilla-ice-cream-15l' => 'vanilla ice cream tub',
        'ube-ice-cream-1l' => 'ube ice cream',
        'chicken-fried-rice' => 'chicken fried rice',
        'beef-stew-microwave' => 'beef stew bowl',
    ];
}
