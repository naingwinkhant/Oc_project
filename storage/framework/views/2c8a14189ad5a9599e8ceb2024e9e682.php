<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'showCategory' => true, 'favourited' => null]));

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

foreach (array_filter((['product', 'showCategory' => true, 'favourited' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $href = route('catalog.product', $product);
    $isFavourite = $favourited ?? app(\App\Cart\FavouriteService::class)->has($product->id);
?>

<article class="group card flex flex-col overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-pop">
    <a href="<?php echo e($href); ?>" class="relative block aspect-4/3 overflow-hidden bg-ink-50">
        <?php if($product->imageUrl()): ?>
            <img src="<?php echo e($product->imageUrl()); ?>" alt="<?php echo e($product->name); ?>" loading="lazy"
                 class="size-full object-cover transition-transform duration-500 group-hover:scale-105">
        <?php else: ?>
            <span class="grid size-full place-items-center text-ink-300">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'box','class' => 'size-10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'box','class' => 'size-10']); ?>
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

        <div class="absolute end-2 top-2 z-10">
            <?php if (isset($component)) { $__componentOriginal9cde4842113a5cd432ae34a5c4ccee1c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9cde4842113a5cd432ae34a5c4ccee1c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.favourite-button','data' => ['product' => $product,'active' => $isFavourite]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('favourite-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isFavourite)]); ?>
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

        <div class="absolute start-2 top-2 flex flex-wrap gap-1.5">
            <?php if($product->isNewArrival()): ?>
                <span class="badge bg-sky-600 text-white ring-sky-700/30">New</span>
            <?php endif; ?>
            <?php if($product->isComingSoon()): ?>
                <span class="badge bg-violet-600 text-white ring-violet-700/30">Coming soon</span>
            <?php endif; ?>
            <?php if($product->isExpired()): ?>
                <span class="badge bg-rose-600 text-white ring-rose-700/30">Expired</span>
            <?php endif; ?>
            <?php if($product->is_featured): ?>
                <span class="badge bg-amber-400 text-amber-950 ring-amber-500/30">Featured</span>
            <?php endif; ?>
            <?php if (! ($product->is_active)): ?>
                <span class="badge bg-ink-900/85 text-white ring-ink-900/20">Hidden</span>
            <?php endif; ?>
        </div>
    </a>

    <div class="flex flex-1 flex-col p-3.5">
        <?php if($showCategory && $product->category): ?>
            <a href="<?php echo e(route('catalog.show', $product->category)); ?>" class="mb-1.5 w-fit">
                <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10"><?php echo e($product->category->name); ?></span>
            </a>
        <?php endif; ?>

        <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-ink-900">
            <a href="<?php echo e($href); ?>" class="transition-colors hover:text-brand-700"><?php echo e($product->name); ?></a>
        </h3>

        <p class="mt-1 truncate font-mono text-[0.6875rem] text-ink-400"><?php echo e($product->sku); ?></p>

        <div class="mt-auto flex items-end justify-between gap-2 pt-3">
            <?php if (isset($component)) { $__componentOriginal5c7c50258000edf57abfef324d310474 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c7c50258000edf57abfef324d310474 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.price','data' => ['product' => $product,'size' => 'sm','class' => 'min-w-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('price'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'size' => 'sm','class' => 'min-w-0']); ?>
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

            <?php if($product->isSellable()): ?>
                <form method="POST" action="<?php echo e(route('cart.store')); ?>" class="shrink-0" data-add-to-cart="<?php echo e($product->id); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-soft btn-sm"
                            title="Add <?php echo e($product->name); ?> to cart"
                            <?php if($product->stock < 10): ?>
                                aria-label="Add <?php echo e($product->name); ?> to cart, <?php echo e($product->stock); ?> left"
                            <?php endif; ?>>
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'cart','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cart','class' => 'size-3.5']); ?>
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
                        <?php if($product->stock < 10): ?>
                            <span class="text-[0.625rem] font-bold tabular-nums"><?php echo e($product->stock); ?></span>
                        <?php else: ?>
                            <span class="sr-only">Add to cart</span>
                        <?php endif; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-1.5">
            <?php if($product->isComingSoon()): ?>
                <span class="badge bg-violet-50 text-violet-700 ring-violet-600/20">
                    On the shelf <?php echo e($product->available_from->format('j M')); ?>

                </span>
            <?php else: ?>
                <span class="badge <?php echo e($product->stockStatusTone()); ?>"><?php echo e($product->stockStatusLabel()); ?></span>
                <?php if($product->expires_at && $product->expiryStatus() !== 'fresh'): ?>
                    <span class="badge <?php echo e($product->expiryStatusTone()); ?>"><?php echo e($product->expiryStatusLabel()); ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if (isset($component)) { $__componentOriginal55a2a145c73971d8b3899d9083ae76a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55a2a145c73971d8b3899d9083ae76a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.freshness','data' => ['product' => $product,'variant' => 'compact','class' => 'mt-1.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('freshness'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'variant' => 'compact','class' => 'mt-1.5']); ?>
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
</article>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/product-card.blade.php ENDPATH**/ ?>