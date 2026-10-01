<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => $category->name,'description' => $category->description ?? 'Browse '.$category->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($category->name),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($category->description ?? 'Browse '.$category->name)]); ?>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <header class="card mb-6 overflow-hidden">
            <div class="flex flex-wrap items-center gap-4 p-5 sm:p-6">
                <?php if($category->imageUrl()): ?>
                    <img src="<?php echo e($category->imageUrl()); ?>" alt="" class="size-16 shrink-0 rounded-xl object-cover sm:size-20">
                <?php else: ?>
                    <span class="grid size-16 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700 sm:size-20">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'tag','class' => 'size-8 sm:size-9']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'tag','class' => 'size-8 sm:size-9']); ?>
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
                <?php endif; ?>

                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold tracking-tight text-ink-900"><?php echo e($category->name); ?></h1>
                    <?php if($category->description): ?>
                        <p class="mt-1 text-sm text-ink-500"><?php echo e($category->description); ?></p>
                    <?php endif; ?>
                    <p class="mt-1.5 text-xs text-ink-400">
                        <?php echo e(number_format($products->total())); ?> <?php echo e(Str::plural('item', $products->total())); ?> available
                    </p>
                </div>
            </div>

            <?php if($children->isNotEmpty()): ?>
                <div class="flex gap-2 overflow-x-auto border-t border-ink-200 bg-ink-50/60 px-4 py-3 sm:px-6">
                    <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('catalog.show', $child)); ?>"
                           class="btn btn-secondary btn-sm shrink-0 whitespace-nowrap">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'folder','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'folder','class' => 'size-3.5']); ?>
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
                            <?php echo e($child->name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="mb-4 flex flex-wrap items-center gap-3">
            <?php if (isset($component)) { $__componentOriginal16e01e9a6d64ca6093093e67d42c7fb1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal16e01e9a6d64ca6093093e67d42c7fb1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-drawer','data' => ['id' => 'category-filters','label' => 'Filters','active' => (int) request()->boolean('in_stock')
                                 + (int) request()->boolean('on_sale')
                                 + (int) request()->boolean('coming_soon')
                                 + (int) request()->filled('q')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-drawer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'category-filters','label' => 'Filters','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) request()->boolean('in_stock')
                                 + (int) request()->boolean('on_sale')
                                 + (int) request()->boolean('coming_soon')
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

                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                        <input type="checkbox" name="coming_soon" value="1" <?php if(request()->boolean('coming_soon')): echo 'checked'; endif; ?> class="checkbox">
                        Coming soon
                    </label>

                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn btn-primary flex-1">Apply filters</button>
                        <a href="<?php echo e(route('catalog.show', $category)); ?>" class="btn btn-ghost">Clear</a>
                    </div>
                </form>

                <?php if($children->isNotEmpty()): ?>
                    <p class="section-title mt-5 mb-2">Aisles</p>

                    <div class="flex flex-wrap gap-1.5">
                        <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('catalog.show', $child)); ?>"
                               class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                   'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition',
                                   'bg-brand-600 text-white' => $child->id === $category->id,
                                   'bg-ink-100 text-ink-700 hover:bg-ink-200' => $child->id !== $category->id,
                               ]); ?>">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $child->iconName(),'class' => 'size-3.5 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child->iconName()),'class' => 'size-3.5 shrink-0']); ?>
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
                                <?php echo e($child->name); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
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
                <span class="font-semibold text-ink-900"><?php echo e(number_format($products->total())); ?></span>
                <?php echo e(Str::plural('item', $products->total())); ?>

            </p>

            <form method="GET" class="ms-auto flex items-center gap-2">
                <?php if(request()->boolean('in_stock')): ?>
                    <input type="hidden" name="in_stock" value="1">
                <?php endif; ?>
                <select name="sort" data-autosubmit class="select select-sm" aria-label="Sort goods">
                    <?php $__currentLoopData = [
                        'name' => 'Name A → Z',
                        'price_asc' => 'Price: low to high',
                        'price_desc' => 'Price: high to low',
                        'newest' => 'Newest arrivals',
                        'popular' => 'Most viewed',
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('sort', 'name') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>

        <?php if($products->isEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'box','title' => 'Nothing here yet','description' => 'This classification has no visible goods right now. Check back soon.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'box','title' => 'Nothing here yet','description' => 'This classification has no visible goods right now. Check back soon.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product,'showCategory' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'show-category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
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
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/catalog/category.blade.php ENDPATH**/ ?>