<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'active' => false, 'size' => 'md']));

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

foreach (array_filter((['product', 'active' => false, 'size' => 'md']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<form method="POST" action="<?php echo e(route('favourites.toggle')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
    <input type="hidden" name="return_to" value="<?php echo e(request()->fullUrl()); ?>">

    <button type="submit"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'grid place-items-center rounded-full transition-all duration-150 active:scale-90',
                'size-9' => $size === 'md',
                'size-10' => $size === 'lg',
                $active
                    ? 'bg-rose-600 text-white ring-1 ring-rose-700/30 hover:bg-rose-700'
                    : 'bg-surface/85 text-ink-400 ring-1 ring-ink-200 backdrop-blur hover:bg-surface hover:text-rose-500',
            ]); ?>"
            title="<?php echo e($active ? 'Remove from favourites' : 'Save to favourites'); ?>"
            aria-label="<?php echo e($active ? 'Remove '.$product->name.' from favourites' : 'Save '.$product->name.' to favourites'); ?>"
            aria-pressed="<?php echo e($active ? 'true' : 'false'); ?>">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'heart','class' => \Illuminate\Support\Arr::toCssClasses([
            'size-5 transition-transform duration-150',
            'fill-rose-600' => $active,
            'fill-none' => ! $active,
        ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'heart','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([
            'size-5 transition-transform duration-150',
            'fill-rose-600' => $active,
            'fill-none' => ! $active,
        ]))]); ?>
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
    </button>
</form>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/favourite-button.blade.php ENDPATH**/ ?>