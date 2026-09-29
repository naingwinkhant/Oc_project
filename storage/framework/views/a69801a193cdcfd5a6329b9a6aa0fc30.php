<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon' => 'cart',
    'href' => null,
    'count' => 0,
    'label' => 'Cart',
    'active' => false,
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
    'icon' => 'cart',
    'href' => null,
    'count' => 0,
    'label' => 'Cart',
    'active' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<a href="<?php echo e($href); ?>"
   <?php echo e($attributes->merge(['class' => 'btn btn-secondary relative shrink-0 px-2.5 sm:px-3'])); ?>

   title="<?php echo e($label); ?><?php echo e($count > 0 ? ' — '.$count.' item'.($count === 1 ? '' : 's') : ''); ?>"
   aria-label="<?php echo e($label); ?>">
    <span class="grid size-5 shrink-0 place-items-center">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $icon,'class' => \Illuminate\Support\Arr::toCssClasses([
            'fill-rose-600 text-rose-600' => $active,
            'fill-none' => ! $active,
        ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([
            'fill-rose-600 text-rose-600' => $active,
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
    </span>

    <span class="hidden lg:inline"><?php echo e($label); ?></span>

    <?php if($count > 0): ?>
        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'absolute -top-1.5 -end-1.5 grid min-w-4.5 place-items-center rounded-full px-1 text-[0.625rem] leading-4 font-bold text-white tabular-nums',
            'bg-rose-600' => $active,
            'bg-brand-600' => ! $active,
        ]); ?>">
            <?php echo e($count > 99 ? '99+' : $count); ?>

        </span>
    <?php endif; ?>
</a>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/icon-button.blade.php ENDPATH**/ ?>