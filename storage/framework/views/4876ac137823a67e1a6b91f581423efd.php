<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => 'New arrivals','description' => 'The latest additions to the shelves']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'New arrivals','description' => 'The latest additions to the shelves']); ?>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <section class="mb-6 overflow-hidden rounded-card bg-sky-700 p-5 text-white shadow-pop sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wider text-sky-200 uppercase">Just landed</p>
                    <h1 class="mt-1 text-xl font-bold tracking-tight sm:text-2xl">New arrivals</h1>
                    <p class="mt-1 max-w-xl text-sm text-sky-100">
                        <?php echo e(number_format($products->total())); ?> <?php echo e(Str::plural('item', $products->total())); ?> added to the
                        shelves in the last few weeks.
                    </p>
                </div>
                <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-white/15 backdrop-blur">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'sparkles','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'size-6']); ?>
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
        </section>

        <div class="mb-4 flex flex-wrap items-center gap-3">
            <?php if (isset($component)) { $__componentOriginal16e01e9a6d64ca6093093e67d42c7fb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal16e01e9a6d64ca6093093e67d42c7fb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-drawer','data' => ['id' => 'arrivals-filters','label' => 'Filters','active' => (int) request()->boolean('in_stock')
                                 + (int) request()->boolean('on_sale')
                                 + (int) request()->filled('q')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-drawer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'arrivals-filters','label' => 'Filters','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) request()->boolean('in_stock')
                                 + (int) request()->boolean('on_sale')
                                 + (int) request()->filled('q'))]); ?>
                <form method="GET" class="space-y-3">
                    <?php if(request('q')): ?>
                        <input type="hidden" name="q" value="<?php echo e(request('q')); ?>">
                    <?php endif; ?>

                    <p class="section-title">Refine</p>

                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                        <input type="checkbox" name="in_stock" value="1" <?php if(request()->boolean('in_stock')): echo 'checked'; endif; ?> class="checkbox">
                        In stock only
                    </label>

                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                        <input type="checkbox" name="on_sale" value="1" <?php if(request()->boolean('on_sale')): echo 'checked'; endif; ?> class="checkbox">
                        On promotion
                    </label>

                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn btn-primary flex-1">Apply filters</button>
                        <a href="<?php echo e(route('catalog.new-arrivals')); ?>" class="btn btn-ghost">Clear</a>
                    </div>
                </form>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal16e01e9a6d64ca6093093e67d42c7fb1)): ?>
<?php $attributes = $__attributesOriginal16e01e9a6d64ca6093093e67d42c7fb1; ?>
<?php unset($__attributesOriginal16e01e9a6d64ca6093093e67d42c7fb1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal16e01e9a6d64ca6093093e67d42c7fb1)): ?>
<?php $component = $__componentOriginal16e01e9a6d64ca6093093e67d42c7fb1; ?>
<?php unset($__componentOriginal16e01e9a6d64ca6093093e67d42c7fb1); ?>
<?php endif; ?>

            <p class="text-sm text-ink-600">
                Sorted by <span class="font-semibold text-ink-900">newest first</span>
            </p>

            <form method="GET" class="ms-auto">
                <select name="sort" data-autosubmit class="select select-sm" aria-label="Sort new arrivals">
                    <?php $__currentLoopData = [
                        'newest' => 'Newest first',
                        'name' => 'Name A → Z',
                        'price_asc' => 'Price: low to high',
                        'price_desc' => 'Price: high to low',
                        'popular' => 'Most viewed',
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('sort', 'newest') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>

        <?php if($products->isEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'sparkles','title' => 'No new arrivals yet','description' => 'Fresh stock lands every few days — check back soon.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'sparkles','title' => 'No new arrivals yet','description' => 'Fresh stock lands every few days — check back soon.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
        <?php else: ?>
            <div class="grid gap-3 grid-cards sm:gap-4">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="mt-6">
                <?php echo e($products->links('pagination::tailwind-simple')); ?>

            </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $attributes = $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd)): ?>
<?php $component = $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd; ?>
<?php unset($__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd); ?>
<?php endif; ?>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/catalog/new-arrivals.blade.php ENDPATH**/ ?>