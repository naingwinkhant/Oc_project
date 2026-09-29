<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'value',
    'icon' => 'chart',
    'tone' => 'brand',
    'hint' => null,
    'trend' => null,
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
    'label',
    'value',
    'icon' => 'chart',
    'tone' => 'brand',
    'hint' => null,
    'trend' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $tones = [
        'brand' => 'bg-brand-50 text-brand-700 ring-brand-600/15',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/15',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-600/15',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-600/15',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-600/15',
    ];
?>

<div class="card p-4 transition-shadow duration-200 hover:shadow-raise sm:p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase"><?php echo e($label); ?></p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-ink-900 tabular-nums sm:text-[1.75rem]"><?php echo e($value); ?></p>
        </div>
        <span class="grid size-10 shrink-0 place-items-center rounded-lg ring-1 ring-inset <?php echo e($tones[$tone] ?? $tones['brand']); ?>">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $icon,'class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'class' => 'size-5']); ?>
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
    </div>

    <?php if($hint || $trend): ?>
        <p class="mt-3 flex items-center gap-1.5 text-xs text-ink-500">
            <?php if($trend): ?>
                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'inline-flex items-center gap-0.5 font-semibold whitespace-nowrap',
                    'text-emerald-600' => str_starts_with((string) $trend, '+'),
                    'text-rose-600' => str_starts_with((string) $trend, '-'),
                ]); ?>">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => str_starts_with((string) $trend, '-') ? 'arrow-down' : 'arrow-up','class' => 'size-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(str_starts_with((string) $trend, '-') ? 'arrow-down' : 'arrow-up'),'class' => 'size-3']); ?>
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
                    <?php echo e(ltrim((string) $trend, '+-')); ?>

                </span>
            <?php endif; ?>
            <?php echo e($hint); ?>

        </p>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/stat-card.blade.php ENDPATH**/ ?>