<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'variant' => 'detail', 'class' => '']));

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

foreach (array_filter((['product', 'variant' => 'detail', 'class' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $fresh = $product->freshness();
    $tone = $product->isExpired()
        ? 'text-rose-700'
        : ($product->isComingSoon() ? 'text-violet-700' : ($product->expiryStatus() === 'expiring' ? 'text-amber-700' : 'text-ink-500'));
?>

<?php if($variant === 'detail'): ?>
    <div class="text-xs leading-relaxed <?php echo e($class); ?>">
        <p class="flex flex-wrap items-center gap-x-3 gap-y-1 font-medium text-ink-700">
            <?php if($fresh['packed']): ?>
                <span class="inline-flex items-center gap-1.5">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'calendar','class' => 'size-3.5 shrink-0 opacity-60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','class' => 'size-3.5 shrink-0 opacity-60']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    Packed <?php echo e($fresh['packed']); ?>

                </span>
            <?php endif; ?>

            <?php if($fresh['best_before']): ?>
                <span class="inline-flex items-center gap-1.5">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'clock','class' => 'size-3.5 shrink-0 opacity-60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','class' => 'size-3.5 shrink-0 opacity-60']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    Best before <?php echo e($fresh['best_before']); ?>

                </span>
            <?php endif; ?>
        </p>

        <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 font-medium <?php echo e($tone); ?>">
            <?php if($fresh['shelf_life']): ?>
                <span><?php echo e($fresh['shelf_life']); ?></span>
            <?php endif; ?>

            <?php if($fresh['remaining']): ?>
                <span><?php echo e($fresh['remaining']); ?></span>
            <?php endif; ?>

            <?php if($fresh['note']): ?>
                <span><?php echo e($fresh['note']); ?></span>
            <?php endif; ?>
        </p>
    </div>
<?php else: ?>
    <?php $line = $product->freshnessLine(); ?>

    <?php if($line): ?>
        <p class="text-[0.6875rem] leading-snug <?php echo e($tone); ?> <?php echo e($class); ?>"><?php echo e($line); ?></p>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/freshness.blade.php ENDPATH**/ ?>