<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'product' => null,
    'price' => null,
    'salePrice' => null,
    'unit' => null,
    'size' => 'md',
    'showUnit' => true,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'product' => null,
    'price' => null,
    'salePrice' => null,
    'unit' => null,
    'size' => 'md',
    'showUnit' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $list = $product?->listPrice() ?? (int) ($price ?? 0);
    $sale = $product?->hasDiscount() ? $product->effectivePrice() : ($salePrice !== null ? (int) $salePrice : null);
    $hasDiscount = $sale !== null && $sale > 0 && $sale < $list;
    $unit ??= $product?->unit;

    $sizes = [
        'sm' => ['current' => 'text-sm font-semibold', 'original' => 'text-[0.6875rem]', 'unit' => 'text-[0.625rem]'],
        'md' => ['current' => 'text-lg font-bold', 'original' => 'text-xs', 'unit' => 'text-[0.6875rem]'],
        'lg' => ['current' => 'text-3xl font-bold', 'original' => 'text-sm', 'unit' => 'text-sm'],
    ];
    $scale = $sizes[$size] ?? $sizes['md'];
?>

<span <?php echo e($attributes->merge(['class' => 'inline-flex flex-wrap items-baseline gap-x-2 gap-y-0.5'])); ?>>
    
    <span class="<?php echo e($scale['current']); ?> tracking-tight text-blue-700 tabular-nums">
        <?php echo e(\App\Support\Money::format($hasDiscount ? $sale : $list)); ?>

        <?php if($showUnit && $unit): ?>
            <span class="<?php echo e($scale['unit']); ?> font-medium text-ink-400">/ <?php echo e($unit); ?></span>
        <?php endif; ?>
    </span>

    
    <?php if($hasDiscount): ?>
        <s class="<?php echo e($scale['original']); ?> font-medium text-rose-600 tabular-nums">
            <?php echo e(\App\Support\Money::format($list)); ?>

        </s>
        <span class="badge bg-rose-50 text-rose-700 ring-rose-600/20">
            −<?php echo e($product?->discountPercent() ?? (int) round(($list - $sale) / $list * 100)); ?>%
        </span>
    <?php endif; ?>
</span>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/price.blade.php ENDPATH**/ ?>