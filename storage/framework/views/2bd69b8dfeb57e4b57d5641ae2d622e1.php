<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['supplier', 'products' => null, 'mode' => 'create']));

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

foreach (array_filter((['supplier', 'products' => null, 'mode' => 'create']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php $isEdit = $mode === 'edit'; ?>

<form method="POST" action="<?php echo e($isEdit ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store')); ?>" class="grid gap-5 lg:grid-cols-3">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="space-y-5 lg:col-span-2">
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Supplier details</h2>
            </div>
            <div class="card-body grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'name','label' => 'Company name','value' => $supplier->name,'required' => true,'placeholder' => 'Metro Fresh Distribution']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'name','label' => 'Company name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->name),'required' => true,'placeholder' => 'Metro Fresh Distribution']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                </div>

                <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'code','label' => 'Supplier code','value' => $supplier->code,'placeholder' => 'Auto-generated','hint' => 'Leave blank to generate one.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'code','label' => 'Supplier code','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->code),'placeholder' => 'Auto-generated','hint' => 'Leave blank to generate one.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'contact_name','label' => 'Contact person','value' => $supplier->contact_name,'placeholder' => 'Juan Dela Cruz']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'contact_name','label' => 'Contact person','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->contact_name),'placeholder' => 'Juan Dela Cruz']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'phone','label' => 'Phone','value' => $supplier->phone,'placeholder' => '+63 917 000 0000']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'phone','label' => 'Phone','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->phone),'placeholder' => '+63 917 000 0000']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'email','label' => 'Email','type' => 'email','value' => $supplier->email,'placeholder' => 'orders@supplier.com']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'email','label' => 'Email','type' => 'email','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->email),'placeholder' => 'orders@supplier.com']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>

                <div class="sm:col-span-2">
                    <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'address','label' => 'Address','type' => 'textarea','value' => $supplier->address,'rows' => 2,'placeholder' => 'Street, city, region']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'address','label' => 'Address','type' => 'textarea','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplier->address),'rows' => 2,'placeholder' => 'Street, city, region']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                </div>

                <div>
                    <label class="label">Status</label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-ink-200 p-3 transition hover:bg-ink-50">
                        <input type="checkbox" name="is_active" value="1"
                               <?php if(old('is_active', $supplier->is_active ?? true)): echo 'checked'; endif; ?> class="checkbox">
                        <span class="text-sm font-medium text-ink-700">Active supplier</span>
                    </label>
                </div>
            </div>
        </section>

        <?php if($isEdit && $products && $products->isNotEmpty()): ?>
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Linked goods</h2>
                </div>
                <ul class="divide-y divide-ink-100">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center justify-between gap-3 px-4 py-2.5 sm:px-5">
                            <span class="truncate text-sm font-medium text-ink-800"><?php echo e($item->name); ?></span>
                            <span class="shrink-0 font-mono text-xs text-ink-400"><?php echo e($item->sku); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </section>
        <?php endif; ?>
    </div>

    <div class="space-y-5">
        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'size-4']); ?>
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
                <?php echo e($isEdit ? 'Save changes' : 'Add supplier'); ?>

            </button>
            <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="btn btn-secondary w-full">Cancel</a>
        </div>
    </div>
</form>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/admin/suppliers/_form.blade.php ENDPATH**/ ?>