<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => $product->name,'description' => $product->brand ? $product->brand.' · '.$product->name : $product->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->name),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->brand ? $product->brand.' · '.$product->name : $product->name)]); ?>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="grid max-h-[26rem] place-items-center bg-ink-50">
                        <?php if($product->imageUrl()): ?>
                            <img src="<?php echo e($product->imageUrl()); ?>" alt="<?php echo e($product->name); ?>" class="size-full object-contain">
                        <?php else: ?>
                            <span class="grid size-full place-items-center text-ink-300">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'box','class' => 'size-16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'box','class' => 'size-16']); ?>
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
                    </div>
                </div>

                <?php if($product->description): ?>
                    <section class="card mt-4">
                        <div class="card-header">
                            <h2 class="card-title">Product details</h2>
                        </div>
                        <div class="card-body">
                            <p class="text-sm leading-relaxed whitespace-pre-line text-ink-600"><?php echo e($product->description); ?></p>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if($credit = $product->imageCredit()): ?>
                    <p class="mt-3 flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[0.6875rem] text-ink-400">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'image','class' => 'size-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'image','class' => 'size-3']); ?>
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
                        <span>Photo:</span>
                        <a href="<?php echo e($credit['source']); ?>" target="_blank" rel="noopener nofollow"
                           class="hover:text-brand-700 hover:underline">
                            <?php echo e(\Illuminate\Support\Str::limit($credit['author'] ?: 'Wikimedia Commons', 40)); ?>

                        </a>
                        <?php if($credit['license']): ?>
                            <span>· <?php echo e($credit['license']); ?></span>
                        <?php endif; ?>
                        <span>· via Wikimedia Commons</span>
                    </p>
                <?php endif; ?>

                <?php if(! empty($product->attributes)): ?>
                    <section class="card mt-4">
                        <div class="card-header">
                            <h2 class="card-title">Specifications</h2>
                        </div>
                        <dl class="grid grid-cols-2 gap-px bg-ink-200 sm:grid-cols-3">
                            <?php $__currentLoopData = $product->attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-white px-4 py-3">
                                    <dt class="text-[0.6875rem] font-semibold tracking-wider text-ink-400 uppercase">
                                        <?php echo e(str_replace('_', ' ', $key)); ?>

                                    </dt>
                                    <dd class="mt-1 text-sm font-medium text-ink-800"><?php echo e($value); ?></dd>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </dl>
                    </section>
                <?php endif; ?>
            </div>

            <aside class="space-y-4">
                <section class="card">
                    <div class="card-body">
                        <?php if($product->category): ?>
                            <a href="<?php echo e(route('catalog.show', $product->category)); ?>" class="mb-2 w-fit">
                                <span class="badge bg-brand-50 text-brand-700 ring-brand-600/15"><?php echo e($product->category->name); ?></span>
                            </a>
                        <?php endif; ?>

                        <h1 class="text-xl font-bold tracking-tight text-ink-900"><?php echo e($product->name); ?></h1>

                        <?php if($product->brand): ?>
                            <p class="mt-1 text-sm text-ink-500">by <span class="font-medium text-ink-700"><?php echo e($product->brand); ?></span></p>
                        <?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginal5c7c50258000edf57abfef324d310474 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c7c50258000edf57abfef324d310474 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.price','data' => ['product' => $product,'size' => 'lg','class' => 'mt-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('price'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'size' => 'lg','class' => 'mt-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c7c50258000edf57abfef324d310474)): ?>
<?php $attributes = $__attributesOriginal5c7c50258000edf57abfef324d310474; ?>
<?php unset($__attributesOriginal5c7c50258000edf57abfef324d310474); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c7c50258000edf57abfef324d310474)): ?>
<?php $component = $__componentOriginal5c7c50258000edf57abfef324d310474; ?>
<?php unset($__componentOriginal5c7c50258000edf57abfef324d310474); ?>
<?php endif; ?>

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <?php if($product->isComingSoon()): ?>
                                <span class="badge bg-violet-600 text-white ring-violet-700/30">Coming soon</span>
                            <?php endif; ?>
                            <span class="badge <?php echo e($product->stockStatusTone()); ?>"><?php echo e($product->stockStatusLabel()); ?></span>
                            <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">
                                <?php echo e($product->stock); ?> <?php echo e($product->unit); ?><?php echo e($product->weight ? ' · '.$product->weight.' kg' : ''); ?>

                            </span>
                            <?php if($product->expires_at): ?>
                                <span class="badge <?php echo e($product->expiryStatusTone()); ?>"><?php echo e($product->expiryStatusLabel()); ?></span>
                            <?php endif; ?>
                            <?php if($product->is_featured): ?>
                                <span class="badge bg-amber-50 text-amber-700 ring-amber-600/20">On promotion</span>
                            <?php endif; ?>
                        </div>

                        <?php if($product->isExpired()): ?>
                            <p class="mt-4 rounded-lg bg-rose-50 p-3 text-xs text-rose-800 ring-1 ring-rose-600/20">
                                This batch expired on <?php echo e($product->expires_at->format('j F Y')); ?> and is no longer for sale.
                            </p>
                        <?php endif; ?>

                        <?php if($product->isComingSoon()): ?>
                            <p class="mt-4 rounded-lg bg-violet-50 p-3 text-xs text-violet-800 ring-1 ring-violet-600/20">
                                This batch is still on its way — it lands on the shelf on
                                <strong class="font-semibold"><?php echo e($product->available_from->format('j F Y')); ?></strong>.
                                Save it to your favourites to find it again.
                            </p>
                        <?php endif; ?>

                        <div class="mt-4 flex flex-wrap items-start gap-2">
                            <?php if($product->isSellable() && ! $product->isOutOfStock()): ?>
                                <form method="POST" action="<?php echo e(route('cart.store')); ?>" class="flex flex-1 gap-2">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">

                                    <label class="sr-only" for="qty">Quantity</label>
                                    <input id="qty" type="number" name="quantity" value="1" min="1" max="99"
                                           class="w-20 rounded-lg border border-ink-300 px-3 py-2.5 text-center text-sm font-semibold tabular-nums focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25 focus:outline-none">

                                    <button type="submit" class="btn btn-primary btn-lg flex-1">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'cart','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cart','class' => 'size-4']); ?>
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
                                        Add to cart
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginal9cde4842113a5cd432ae34a5c4ccee1c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9cde4842113a5cd432ae34a5c4ccee1c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.favourite-button','data' => ['product' => $product,'size' => 'lg','active' => app(\App\Cart\FavouriteService::class)->has($product->id)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('favourite-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'size' => 'lg','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(app(\App\Cart\FavouriteService::class)->has($product->id))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9cde4842113a5cd432ae34a5c4ccee1c)): ?>
<?php $attributes = $__attributesOriginal9cde4842113a5cd432ae34a5c4ccee1c; ?>
<?php unset($__attributesOriginal9cde4842113a5cd432ae34a5c4ccee1c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9cde4842113a5cd432ae34a5c4ccee1c)): ?>
<?php $component = $__componentOriginal9cde4842113a5cd432ae34a5c4ccee1c; ?>
<?php unset($__componentOriginal9cde4842113a5cd432ae34a5c4ccee1c); ?>
<?php endif; ?>
                        </div>

                        <?php if (! ($product->is_active)): ?>
                            <p class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800 ring-1 ring-amber-600/20">
                                This item is currently unavailable.
                            </p>
                        <?php endif; ?>

                        <dl class="mt-5 space-y-2.5 border-t border-ink-100 pt-4 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-ink-500">SKU</dt>
                                <dd class="font-mono text-xs font-semibold text-ink-800" data-copy="<?php echo e($product->sku); ?>" role="button" tabindex="0">
                                    <?php echo e($product->sku); ?>

                                    <span data-copy-label class="text-[0.625rem] font-normal text-ink-400"></span>
                                </dd>
                            </div>
                            <?php if($product->barcode): ?>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-ink-500">Barcode</dt>
                                    <dd class="font-mono text-xs font-semibold text-ink-800"><?php echo e($product->barcode); ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if($product->weight): ?>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-ink-500">Weight</dt>
                                    <dd class="font-semibold text-ink-800 tabular-nums"><?php echo e($product->weight); ?> kg</dd>
                                </div>
                            <?php endif; ?>
                            <?php if($product->produced_at || $product->expires_at || $product->isComingSoon()): ?>
                                <div class="flex flex-col gap-1 rounded-lg bg-ink-50 p-3 sm:flex-row sm:items-center sm:justify-between sm:gap-3">
                                    <dt class="text-ink-500">Freshness</dt>
                                    <dd class="sm:text-end">
                                        <?php if (isset($component)) { $__componentOriginal55a2a145c73971d8b3899d9083ae76a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55a2a145c73971d8b3899d9083ae76a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.freshness','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('freshness'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55a2a145c73971d8b3899d9083ae76a0)): ?>
<?php $attributes = $__attributesOriginal55a2a145c73971d8b3899d9083ae76a0; ?>
<?php unset($__attributesOriginal55a2a145c73971d8b3899d9083ae76a0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55a2a145c73971d8b3899d9083ae76a0)): ?>
<?php $component = $__componentOriginal55a2a145c73971d8b3899d9083ae76a0; ?>
<?php unset($__componentOriginal55a2a145c73971d8b3899d9083ae76a0); ?>
<?php endif; ?>
                                    </dd>
                                </div>
                            <?php endif; ?>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-ink-500">Classification</dt>
                                <dd class="font-semibold text-ink-800"><?php echo e($product->category?->name ?? '—'); ?></dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <?php if($movements->isNotEmpty()): ?>
                    <section class="card">
                        <div class="card-header">
                            <h2 class="card-title">Recent stock activity</h2>
                        </div>
                        <ul class="divide-y divide-ink-100">
                            <?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-center justify-between gap-3 px-4 py-2.5 sm:px-5">
                                    <span class="text-xs font-medium text-ink-700"><?php echo e($movement->type->label()); ?></span>
                                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                        'text-sm font-bold tabular-nums',
                                        'text-emerald-600' => $movement->isPositive(),
                                        'text-rose-600' => ! $movement->isPositive(),
                                    ]); ?>"><?php echo e($movement->isPositive() ? '+' : ''); ?><?php echo e($movement->quantity); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </section>
                <?php endif; ?>
            </aside>
        </div>

        <?php if($related->isNotEmpty()): ?>
            <section class="mt-10">
                <h2 class="mb-4 text-lg font-bold tracking-tight text-ink-900">You might also like</h2>
                <div class="grid gap-3 grid-cards sm:gap-4">
                    <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $item]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item)]); ?>
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
            </section>
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
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/catalog/product.blade.php ENDPATH**/ ?>