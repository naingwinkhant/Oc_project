<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => 'Goods catalogue','description' => 'Browse every item by classification']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Goods catalogue','description' => 'Browse every item by classification']); ?>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <section class="mb-6 overflow-hidden rounded-card bg-brand-700 p-5 text-white shadow-pop sm:p-8">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'grid items-center gap-3 sm:gap-4',
                'lg:grid-cols-[minmax(0,21rem)_minmax(0,1fr)]' => count($promo ?? []),
            ]); ?>">
                <?php if(count($promo ?? [])): ?>
                    <?php if (isset($component)) { $__componentOriginal841bc97891905ce3addcf5c060b9742b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal841bc97891905ce3addcf5c060b9742b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.promo-carousel','data' => ['slides' => $promo]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('promo-carousel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['slides' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($promo)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal841bc97891905ce3addcf5c060b9742b)): ?>
<?php $attributes = $__attributesOriginal841bc97891905ce3addcf5c060b9742b; ?>
<?php unset($__attributesOriginal841bc97891905ce3addcf5c060b9742b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal841bc97891905ce3addcf5c060b9742b)): ?>
<?php $component = $__componentOriginal841bc97891905ce3addcf5c060b9742b; ?>
<?php unset($__componentOriginal841bc97891905ce3addcf5c060b9742b); ?>
<?php endif; ?>
                <?php endif; ?>

                <div class="min-w-0">
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        <?php echo e(config('shop.hero.headline')); ?>

                    </h1>
                    <p class="mt-1.5 max-w-xl text-base text-brand-100 sm:text-base">
                        <?php echo e(config('shop.hero.subline')); ?>

                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <a href="<?php echo e(route('catalog.index', ['on_sale' => 1])); ?>"
                           class="btn border-0 bg-white text-brand-800 hover:bg-white/90">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'tag','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'tag','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e(config('shop.hero.cta')); ?>

                        </a>
                        <a href="<?php echo e(route('login')); ?>" class="btn border-0 bg-white/15 text-white backdrop-blur hover:bg-white/25">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'dashboard','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'dashboard','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <span class="hidden sm:inline">Staff sign in</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <details class="card mb-4 lg:hidden" id="mobile-filters">
            <summary class="flex cursor-pointer list-none items-center gap-2 px-4 py-3 text-sm font-semibold text-ink-900">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'sliders','class' => 'size-4 text-ink-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sliders','class' => 'size-4 text-ink-500']); ?>
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
                Classifications &amp; filters
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-down','class' => 'ms-auto size-4 text-ink-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','class' => 'ms-auto size-4 text-ink-400']); ?>
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
            </summary>
            <div class="space-y-3 border-t border-ink-200 p-4">
                <div class="flex flex-wrap gap-1.5">
                    <a href="<?php echo e(route('catalog.index')); ?>"
                       class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                           'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                           'bg-brand-600 text-white' => ! $selectedCategory,
                           'bg-ink-100 text-ink-700 hover:bg-ink-200' => $selectedCategory,
                       ]); ?>">All goods</a>

                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('catalog.show', $category)); ?>"
                           class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                               'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                               'bg-brand-600 text-white' => $selectedCategory?->id === $category->id,
                               'bg-ink-100 text-ink-700 hover:bg-ink-200' => $selectedCategory?->id !== $category->id,
                           ]); ?>"><?php echo e($category->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-4 border-t border-ink-100 pt-3">
                    <?php if(request('q')): ?>
                        <input type="hidden" name="q" value="<?php echo e(request('q')); ?>">
                    <?php endif; ?>
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" name="in_stock" value="1" <?php if(request()->boolean('in_stock')): echo 'checked'; endif; ?> class="checkbox">
                        In stock only
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" name="on_sale" value="1" <?php if(request()->boolean('on_sale')): echo 'checked'; endif; ?> class="checkbox">
                        On promotion
                    </label>
                    <button type="submit" class="btn btn-primary btn-sm ms-auto">Apply</button>
                </form>
            </div>
        </details>

        <div class="grid gap-6 lg:grid-cols-4">

            <aside class="hidden lg:col-span-1 lg:block">
                <div class="lg:sticky lg:top-36">
                    <section class="card mb-4">
                        <div class="card-header">
                            <h2 class="card-title">Classifications</h2>
                        </div>
                        <div class="max-h-[22rem] overflow-y-auto p-2">
                            <a href="<?php echo e(route('catalog.index')); ?>"
                               class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link !py-2', 'nav-link-active' => ! $selectedCategory]); ?>">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'grid','class' => 'size-4 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'grid','class' => 'size-4 shrink-0']); ?>
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
                                <span class="flex-1">All goods</span>
                            </a>

                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <a href="<?php echo e(route('catalog.show', $category)); ?>"
                                       class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link !py-2', 'nav-link-active' => $selectedCategory?->id === $category->id]); ?>">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'tag','class' => 'size-4 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'tag','class' => 'size-4 shrink-0']); ?>
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
                                        <span class="flex-1 truncate"><?php echo e($category->name); ?></span>
                                        <span class="shrink-0 text-xs text-ink-400 tabular-nums"><?php echo e($category->branch_products_count); ?></span>
                                    </a>

                                    <?php if($category->children->isNotEmpty()): ?>
                                        <ul class="ms-6 mt-0.5 space-y-0.5 border-s border-ink-200 ps-2">
                                            <?php $__currentLoopData = $category->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <a href="<?php echo e(route('catalog.show', $child)); ?>"
                                                       class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                           'block truncate rounded-md px-2.5 py-1.5 text-[0.8125rem] transition-colors',
                                                           'bg-brand-50 font-semibold text-brand-700' => $selectedCategory?->id === $child->id,
                                                           'text-ink-600 hover:bg-ink-100 hover:text-ink-900' => $selectedCategory?->id !== $child->id,
                                                       ]); ?>"><?php echo e($child->name); ?></a>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>

                    <section class="card">
                        <div class="card-header">
                            <h2 class="card-title">Refine</h2>
                        </div>
                        <form method="GET" class="card-body space-y-3">
                            <?php if(request('q')): ?>
                                <input type="hidden" name="q" value="<?php echo e(request('q')); ?>">
                            <?php endif; ?>

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

                            <button type="submit" class="btn btn-primary btn-sm w-full">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'filter','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'filter','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Apply
                            </button>
                            <a href="<?php echo e(route('catalog.index')); ?>" class="btn btn-ghost btn-sm w-full">Clear all</a>
                        </form>
                    </section>
                </div>
            </aside>

            <div class="lg:col-span-3">
                <div class="mb-4 flex flex-wrap items-center gap-3">
                    <p class="text-sm text-ink-600">
                        <span class="font-semibold text-ink-900"><?php echo e(number_format($products->total())); ?></span>
                        <?php echo e(Str::plural('item', $products->total())); ?>

                        <?php if(request('q')): ?>
                            matching <span class="font-semibold text-ink-900">“<?php echo e(request('q')); ?>”</span>
                        <?php endif; ?>
                    </p>

                    <form method="GET" class="ms-auto">
                        <?php $__currentLoopData = request()->except(['sort', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e(is_array($value) ? '' : $value); ?>">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'search','title' => 'No goods matched','description' => 'Try a different keyword, or browse a classification from the menu.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'search','title' => 'No goods matched','description' => 'Try a different keyword, or browse a classification from the menu.']); ?>
                         <?php $__env->slot('action', null, []); ?> 
                            <a href="<?php echo e(route('catalog.index')); ?>" class="btn btn-primary btn-sm">Reset search</a>
                         <?php $__env->endSlot(); ?>
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
        </div>
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
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/catalog/index.blade.php ENDPATH**/ ?>