<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['node']));

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

foreach (array_filter((['node']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $children = $node['children'] ?? [];
    $badge = fn (int $count) => $count > 0
        ? "rounded-full bg-ink-100 px-1.5 py-0.5 text-[0.625rem] font-semibold text-ink-600 tabular-nums"
        : "rounded-full bg-amber-50 px-1.5 py-0.5 text-[0.625rem] font-semibold text-amber-700 tabular-nums";
?>

<div class="group flex items-center gap-2">
    <?php if(count($children)): ?>
        <button type="button" data-tree-toggle="cat-<?php echo e($node['id']); ?>"
                class="grid size-6 shrink-0 place-items-center rounded text-ink-400 transition hover:bg-ink-200/70 hover:text-ink-700"
                aria-label="Toggle sub-classifications">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','dataTreeChevron' => true,'class' => 'size-3.5 transition-transform duration-150']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','data-tree-chevron' => true,'class' => 'size-3.5 transition-transform duration-150']); ?>
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
    <?php else: ?>
        <span class="size-6 shrink-0"></span>
    <?php endif; ?>

    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'grid size-8 shrink-0 place-items-center rounded-lg',
        'bg-brand-100 text-brand-700' => ($node['depth'] ?? 0) === 0,
        'bg-ink-100 text-ink-500' => ($node['depth'] ?? 0) > 0,
    ]); ?>">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ($node['depth'] ?? 0) === 0 ? 'tag' : 'folder','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($node['depth'] ?? 0) === 0 ? 'tag' : 'folder'),'class' => 'size-4']); ?>
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

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
            <p class="truncate text-sm font-semibold text-ink-900"><?php echo e($node['name']); ?></p>
            <?php if (! ($node['is_active'])): ?>
                <span class="badge bg-ink-100 text-ink-500 ring-ink-500/10">Hidden</span>
            <?php endif; ?>
        </div>
        <p class="truncate font-mono text-[0.6875rem] text-ink-400">/<?php echo e($node['slug']); ?></p>
    </div>

    <span class="<?php echo e($badge($node['total_products_count'] ?? 0)); ?> hidden shrink-0 sm:inline" title="Items in this branch">
        <?php echo e($node['total_products_count'] ?? 0); ?> items
    </span>

    <div class="flex shrink-0 items-center gap-0.5 opacity-100 transition sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
        <a href="<?php echo e(route('catalog.show', $node['id'])); ?>" class="btn-icon" title="View in catalogue" target="_blank" rel="noopener">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'eye','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'eye','class' => 'size-4']); ?>
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
        </a>
        <a href="<?php echo e(route('admin.categories.create', ['parent' => $node['id']])); ?>" class="btn-icon" title="Add sub-classification">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'size-4']); ?>
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
        </a>
        <a href="<?php echo e(route('admin.categories.edit', $node['id'])); ?>" class="btn-icon" title="Edit">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'pencil','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'pencil','class' => 'size-4']); ?>
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
        </a>
        <?php if (isset($component)) { $__componentOriginalec2502b834f860c8e30d229aa8f280e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalec2502b834f860c8e30d229aa8f280e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-button','data' => ['name' => '','icon' => 'trash','label' => 'Delete '.e($node['name']).'','action' => route('admin.categories.destroy', $node['id']),'confirm' => 'Delete '.$node['name'].'? Sub-classifications will move to the top level.','class' => 'btn-icon text-rose-600 hover:bg-rose-50 hover:text-rose-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => '','icon' => 'trash','label' => 'Delete '.e($node['name']).'','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.categories.destroy', $node['id'])),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Delete '.$node['name'].'? Sub-classifications will move to the top level.'),'class' => 'btn-icon text-rose-600 hover:bg-rose-50 hover:text-rose-700']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalec2502b834f860c8e30d229aa8f280e2)): ?>
<?php $attributes = $__attributesOriginalec2502b834f860c8e30d229aa8f280e2; ?>
<?php unset($__attributesOriginalec2502b834f860c8e30d229aa8f280e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalec2502b834f860c8e30d229aa8f280e2)): ?>
<?php $component = $__componentOriginalec2502b834f860c8e30d229aa8f280e2; ?>
<?php unset($__componentOriginalec2502b834f860c8e30d229aa8f280e2); ?>
<?php endif; ?>
    </div>
</div>

<?php if(count($children)): ?>
    <ul id="cat-<?php echo e($node['id']); ?>" class="mt-1.5 ms-8 space-y-1.5 border-s border-ink-200 ps-3">
        <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <?php if (isset($component)) { $__componentOriginale0a8038922dd42a8a3386056093c946e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale0a8038922dd42a8a3386056093c946e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.category-node','data' => ['node' => $child]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.category-node'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['node' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale0a8038922dd42a8a3386056093c946e)): ?>
<?php $attributes = $__attributesOriginale0a8038922dd42a8a3386056093c946e; ?>
<?php unset($__attributesOriginale0a8038922dd42a8a3386056093c946e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale0a8038922dd42a8a3386056093c946e)): ?>
<?php $component = $__componentOriginale0a8038922dd42a8a3386056093c946e; ?>
<?php unset($__componentOriginale0a8038922dd42a8a3386056093c946e); ?>
<?php endif; ?>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
<?php endif; ?>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/admin/category-node.blade.php ENDPATH**/ ?>