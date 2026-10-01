<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => 'Your cart','description' => 'Review the goods in your cart']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Your cart','description' => 'Review the goods in your cart']); ?>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Your cart</h1>
                <p class="mt-0.5 text-sm text-ink-500">
                    <?php echo e($summary['count']); ?> <?php echo e(Str::plural('item', $summary['count'])); ?> ready to check out
                </p>
            </div>

            <?php if(! $items->isEmpty()): ?>
                <form method="POST" action="<?php echo e(route('cart.clear')); ?>"
                      onsubmit="return confirm('Empty your whole cart?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'size-4']); ?>
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
                        Empty cart
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if($items->isEmpty()): ?>
            <div class="card">
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'cart','title' => 'Your cart is empty','description' => 'Browse the catalogue and add the goods you need — we deliver across Yangon and beyond.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'cart','title' => 'Your cart is empty','description' => 'Browse the catalogue and add the goods you need — we deliver across Yangon and beyond.']); ?>
                     <?php $__env->slot('action', null, []); ?> 
                        <a href="<?php echo e(route('catalog.index')); ?>" class="btn btn-primary">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'store','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'store','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Start shopping
                        </a>
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
            </div>
        <?php else: ?>
            <?php if($blocked->isNotEmpty()): ?>
                <div class="mb-5 rounded-lg bg-rose-50 p-4 ring-1 ring-rose-600/20">
                    <p class="flex items-center gap-2 text-sm font-semibold text-rose-800">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','class' => 'size-4 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','class' => 'size-4 shrink-0']); ?>
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
                        Some items can no longer be sold
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-rose-700">
                        <?php $__currentLoopData = $blocked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <span class="font-semibold"><?php echo e($line['product']->name); ?></span> — <?php echo e($line['reason']); ?>

                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <p class="mt-2 text-xs text-rose-700">Remove them to continue to checkout.</p>
                </div>
            <?php endif; ?>
            <div class="grid items-start gap-5 lg:grid-cols-3">

                <div class="card overflow-hidden lg:col-span-2">
                    <ul class="divide-y divide-ink-100">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $product = $item['product']; ?>
                            <li class="flex gap-3 p-4 sm:gap-4 sm:p-5">
                                <a href="<?php echo e(route('catalog.product', $product)); ?>" class="shrink-0">
                                    <?php if($product->imageUrl()): ?>
                                        <img src="<?php echo e($product->imageUrl()); ?>" alt="" loading="lazy"
                                             class="size-20 rounded-lg object-cover ring-1 ring-ink-200 sm:size-24">
                                    <?php else: ?>
                                        <span class="grid size-20 place-items-center rounded-lg bg-ink-100 text-ink-400 sm:size-24">
                                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'box','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'box','class' => 'size-7']); ?>
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
                                </a>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a href="<?php echo e(route('catalog.product', $product)); ?>"
                                                       class="line-clamp-2 text-sm font-semibold text-ink-900 hover:text-brand-700">
                                                        <?php echo e($product->name); ?>

                                                    </a>
                                                    <p class="mt-0.5 font-mono text-[0.6875rem] text-ink-400"><?php echo e($product->sku); ?></p>
                                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                                        <span class="badge <?php echo e($product->stockStatusTone()); ?>"><?php echo e($product->stockStatusLabel()); ?></span>
                                                        <?php if($product->expires_at): ?>
                                                            <span class="badge <?php echo e($product->expiryStatusTone()); ?>"><?php echo e($product->expiryStatusLabel()); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (isset($component)) { $__componentOriginal55a2a145c73971d8b3899d9083ae76a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55a2a145c73971d8b3899d9083ae76a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.freshness','data' => ['product' => $product,'variant' => 'compact','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('freshness'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'variant' => 'compact','class' => 'mt-1']); ?>
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
                                                </div>

                                                <form method="POST" action="<?php echo e(route('cart.destroy', $product->id)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn-icon hover:bg-rose-50 hover:text-rose-600"
                                                            title="Remove <?php echo e($product->name); ?>" aria-label="Remove item">
                                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'size-4']); ?>
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
                                            </div>

                                    <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
                                        <form method="POST" action="<?php echo e(route('cart.update')); ?>"
                                              class="flex items-center gap-2">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">

                                            <?php $maxQuantity = min(99, max(1, (int) $product->stock)); ?>

                                            <label class="sr-only" for="qty-<?php echo e($product->id); ?>">Quantity</label>
                                            <div class="flex items-center rounded-lg border border-ink-300 bg-surface">
                                                <button type="button" class="grid size-8 place-items-center rounded-l-lg text-ink-500 transition hover:bg-ink-100"
                                                        data-step="-1" data-target="qty-<?php echo e($product->id); ?>" aria-label="Decrease quantity">
                                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'minus','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'minus','class' => 'size-3.5']); ?>
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
                                                <input id="qty-<?php echo e($product->id); ?>" type="number" name="quantity"
                                                       value="<?php echo e($item['quantity']); ?>" min="0" max="<?php echo e($maxQuantity); ?>"
                                                       class="w-12 border-x border-ink-200 py-1.5 text-center text-sm font-semibold tabular-nums focus:outline-none">
                                                <button type="button" class="grid size-8 place-items-center rounded-r-lg text-ink-500 transition hover:bg-ink-100"
                                                        data-step="1" data-target="qty-<?php echo e($product->id); ?>" aria-label="Increase quantity">
                                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'size-3.5']); ?>
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
                                            </div>

                                            <button type="submit" class="btn btn-ghost btn-sm">Update</button>
                                        </form>

                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-blue-700 tabular-nums">
                                                <?php echo e(\App\Support\Money::format($item['line_total'])); ?>

                                            </p>
                                            <p class="text-[0.6875rem] text-ink-400 tabular-nums">
                                                <?php if($product->hasDiscount()): ?>
                                                    <s class="text-rose-600"><?php echo e(\App\Support\Money::format($product->price)); ?></s>
                                                    <span class="ms-1">× <?php echo e($item['quantity']); ?></span>
                                                <?php else: ?>
                                                    <?php echo e(\App\Support\Money::format($product->price)); ?> × <?php echo e($item['quantity']); ?>

                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>

                <aside class="card lg:sticky lg:top-36">
                    <div class="card-header">
                        <h2 class="card-title">Order summary</h2>
                    </div>
                    <div class="card-body space-y-3">
                        
                        <ul class="divide-y divide-ink-100 border-b border-ink-100 pb-1">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $line = $item['product']; ?>
                                <li class="flex items-baseline justify-between gap-3 py-1.5 text-xs">
                                    <span class="min-w-0 flex-1 truncate text-ink-700">
                                        <?php echo e($line->name); ?>

                                        <span class="text-ink-400">
                                            &times; <?php echo e($item['quantity']); ?> <?php echo e($line->unit); ?>

                                        </span>
                                    </span>
                                    <span class="shrink-0 text-right tabular-nums">
                                        <span class="block font-semibold text-ink-900">
                                            <?php echo e(\App\Support\Money::format($item['line_total'])); ?>

                                        </span>
                                        <span class="block text-[0.625rem] text-ink-400">
                                            <?php echo e(\App\Support\Money::format($line->effectivePrice())); ?> each
                                        </span>
                                    </span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>

                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <span class="text-ink-600">
                                Goods subtotal
                                <span class="block text-[0.6875rem] text-ink-400">
                                    sum of each item's price &times; its amount
                                </span>
                            </span>
                            <span class="text-end font-semibold text-ink-900 tabular-nums">
                                <?php echo e($summary['subtotal_formatted']); ?>

                                <span class="block text-[0.6875rem] font-normal text-ink-400">
                                    <?php echo e($summary['count']); ?> <?php echo e(Str::plural('unit', $summary['count'])); ?>

                                </span>
                            </span>
                        </div>

                        <?php if($summary['savings'] > 0): ?>
                            <div class="flex items-baseline justify-between gap-3 text-sm">
                                <span class="text-ink-600">
                                    Promotions
                                    <span class="block text-[0.6875rem] text-ink-400">
                                        was <?php echo e($summary['undiscounted_formatted']); ?>

                                    </span>
                                </span>
                                <span class="text-end font-semibold text-emerald-700 tabular-nums">
                                    &minus;<?php echo e($summary['savings_formatted']); ?>

                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <span class="text-ink-600">Delivery</span>
                            <?php if($isFreeDelivery): ?>
                                <span class="badge bg-emerald-50 text-emerald-700 ring-emerald-600/20">Free</span>
                            <?php else: ?>
                                <span class="text-end">
                                    <span class="block font-semibold text-ink-900 tabular-nums">
                                        <?php echo e($summary['delivery_range']['min_formatted']); ?> – <?php echo e($summary['delivery_range']['max_formatted']); ?>

                                    </span>
                                    <span class="block text-[0.6875rem] text-ink-400">by township</span>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-baseline justify-between gap-3 border-t border-ink-200 pt-3">
                            <span class="text-sm font-semibold text-ink-900">Total</span>
                            <span class="text-end text-lg font-bold text-blue-700 tabular-nums">
                                <?php echo e(\App\Support\Money::format($summary['subtotal'] + ($isFreeDelivery ? 0 : $summary['delivery_range']['min']))); ?>

                                <span class="block text-[0.6875rem] font-normal text-ink-400">
                                    plus delivery for your township
                                </span>
                            </span>
                        </div>
                        <?php if (! ($isFreeDelivery)): ?>
                            <p class="rounded-lg bg-brand-50 p-2.5 text-xs text-brand-800">
                                Add <strong class="font-semibold"><?php echo e(\App\Support\Money::format($amountUntilFree)); ?></strong>
                                more for free delivery anywhere in the country.
                            </p>
                        <?php endif; ?>

                        <?php if($blocked->isNotEmpty()): ?>
                            <span class="btn btn-secondary btn-lg w-full pointer-events-none opacity-50">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','class' => 'size-4']); ?>
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
                                Remove unavailable items
                            </span>
                        <?php else: ?>
                            <a href="<?php echo e(route('checkout.create')); ?>" class="btn btn-primary btn-lg w-full">
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
                                Checkout
                            </a>
                        <?php endif; ?>

                        <p class="flex items-start gap-1.5 text-[0.6875rem] text-ink-400">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','class' => 'mt-px size-3 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','class' => 'mt-px size-3 shrink-0']); ?>
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
                            Pay by KBZPay, Wave Money, AyaPay, UAB Pay or cash on delivery.
                        </p>
                    </div>
                </aside>
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
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/cart/index.blade.php ENDPATH**/ ?>