<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['field', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null, 'placeholder' => null, 'rows' => 3]));

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

foreach (array_filter((['field', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null, 'placeholder' => null, 'rows' => 3]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $current = old($field, $value);
    $hasError = $errors->has($field);
?>

<div>
    <label for="<?php echo e($field); ?>" class="label"><?php echo e($label); ?></label>

    <?php if($type === 'textarea'): ?>
        <textarea id="<?php echo e($field); ?>" name="<?php echo e($field); ?>" rows="<?php echo e($rows); ?>"
                  <?php if($required): ?> required <?php endif; ?>
                  <?php if($placeholder): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
                  class="input <?php if($hasError): ?> input-error <?php endif; ?>"><?php echo e($current); ?></textarea>
    <?php elseif($type === 'select'): ?>
        <select id="<?php echo e($field); ?>" name="<?php echo e($field); ?>"
                <?php if($required): ?> required <?php endif; ?>
                class="select <?php if($hasError): ?> input-error <?php endif; ?>">
            <?php echo e($slot); ?>

        </select>
    <?php else: ?>
        <input id="<?php echo e($field); ?>" name="<?php echo e($field); ?>" type="<?php echo e($type); ?>" value="<?php echo e($current); ?>"
               <?php if($required): ?> required <?php endif; ?>
               <?php if($placeholder): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
               class="input <?php if($hasError): ?> input-error <?php endif; ?>">
    <?php endif; ?>

    <?php if($hasError): ?>
        <p class="help-error"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','class' => 'size-3.5 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','class' => 'size-3.5 shrink-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e($errors->first($field)); ?></p>
    <?php elseif($hint): ?>
        <p class="mt-1.5 text-xs text-ink-400"><?php echo e($hint); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/form-field.blade.php ENDPATH**/ ?>