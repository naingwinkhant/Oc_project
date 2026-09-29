<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['slides' => []]));

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

foreach (array_filter((['slides' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(count($slides) > 0): ?>
    <div class="promo-carousel relative h-full min-w-0"
         data-carousel
         data-autoplay="<?php echo e(config('shop.promo.autoplay_ms', 6000)); ?>"
         role="group"
         aria-roledescription="carousel"
         aria-label="Featured promotions">
        
        <div class="relative h-52 w-full overflow-hidden rounded-xl bg-brand-800 shadow-pop ring-1 ring-white/15 sm:h-60 lg:h-64">
            <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'promo-slide absolute inset-0 transition-opacity duration-500 ease-out',
                    'opacity-100' => $loop->first,
                    'opacity-0' => ! $loop->first,
                ]); ?>"
                     data-slide
                     aria-hidden="<?php echo e($loop->first ? 'false' : 'true'); ?>">

                    <?php if($slide['type'] === 'video'): ?>
                        <video class="size-full object-cover"
                               data-promo-video
                               muted loop playsinline preload="metadata"
                               <?php if($slide['poster'] ?? null): ?>
                                   poster="<?php echo e($slide['poster']); ?>"
                               <?php endif; ?>
                               <?php if(! $loop->first): echo 'disabled'; endif; ?>>
                            <source src="<?php echo e($slide['src']); ?>" type="video/mp4">
                        </video>
                    <?php else: ?>
                        <img src="<?php echo e($slide['src']); ?>"
                             alt="<?php echo e($slide['alt'] ?? $slide['title']); ?>"
                             class="size-full object-cover"
                             loading="<?php echo e($loop->first ? 'eager' : 'lazy'); ?>">
                    <?php endif; ?>

                    
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/25 to-transparent"></div>

                    <div class="absolute inset-x-0 bottom-0 p-3.5">
                        <?php if($slide['eyebrow'] ?? null): ?>
                            <p class="truncate text-[0.5625rem] font-semibold tracking-widest text-brand-200 uppercase">
                                <?php echo e($slide['eyebrow']); ?>

                            </p>
                        <?php endif; ?>

                        <p class="mt-0.5 line-clamp-1 text-sm font-bold text-white">
                            <?php echo e($slide['title']); ?>

                        </p>

                        <div class="mt-0.5 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                            <?php if($slide['price'] ?? null): ?>
                                <span class="text-sm font-bold text-white tabular-nums"><?php echo e($slide['price']); ?></span>
                            <?php endif; ?>

                            <?php if($slide['was'] ?? null): ?>
                                <s class="text-[0.6875rem] text-brand-200 tabular-nums"><?php echo e($slide['was']); ?></s>
                            <?php endif; ?>

                            <?php if($slide['text'] ?? null): ?>
                                <span class="truncate text-[0.6875rem] text-brand-100"><?php echo e($slide['text']); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if($slide['url'] ?? null): ?>
                            <a href="<?php echo e($slide['url']); ?>"
                               class="btn btn-lg mt-2.5 border-0 bg-white px-4 py-2 text-sm text-brand-800 hover:bg-white sm:px-5 sm:py-2.5 sm:text-base">
                                View item
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if(count($slides) > 1): ?>
                
                <div class="absolute end-2 top-2 z-10 flex gap-1">
                    <button type="button" data-carousel-prev
                            class="grid size-7 place-items-center rounded-full bg-brand-950/45 text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-brand-950/70"
                            aria-label="Previous slide">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-left','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-left','class' => 'size-3.5']); ?>
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

                    <button type="button" data-carousel-next
                            class="grid size-7 place-items-center rounded-full bg-brand-950/45 text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-brand-950/70"
                            aria-label="Next slide">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-right','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','class' => 'size-3.5']); ?>
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

                
                <div class="absolute start-2 top-2 z-10 flex items-center gap-1.5 rounded-full bg-brand-950/30 px-2 py-1.5 backdrop-blur"
                     data-carousel-dots>
                    <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button"
                                data-carousel-dot="<?php echo e($index); ?>"
                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'h-1.5 rounded-full transition-all',
                                    'w-6 bg-white' => $loop->first,
                                    'w-1.5 bg-white/40 hover:bg-white/70' => ! $loop->first,
                                ]); ?>"
                                aria-label="Slide <?php echo e($index + 1); ?> of <?php echo e(count($slides)); ?>"></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/promo-carousel.blade.php ENDPATH**/ ?>