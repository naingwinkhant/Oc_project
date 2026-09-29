<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'description' => null,
]));

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

foreach (array_filter(([
    'title' => null,
    'description' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $user = auth()->user();
    $roots = \App\Models\Category::query()->active()->roots()->with(['children' => fn ($q) => $q->active()->orderBy('position')])->get();
    $cartCount = app(\App\Cart\CartService::class)->count();
    $favouriteCount = app(\App\Cart\FavouriteService::class)->count();

    // Exactly one navigation entry may look selected, so work out which one.
    $activeCategory = request()->routeIs('catalog.show') ? request()->route('category') : null;
    $activeProduct = request()->routeIs('catalog.product') ? request()->route('product') : null;
    $activeRootSlug = ($activeCategory ?? $activeProduct?->category)?->root()->slug;
    $activeChildSlug = $activeCategory && ! $activeCategory->isRoot() ? $activeCategory->slug : null;
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ? $title.' · ' : ''); ?><?php echo e(config('app.name')); ?></title>
    <meta name="description" content="<?php echo e($description ?? config('app.name')); ?>">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" href="/favicon.ico">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-full">
<div class="flex min-h-full flex-col">

    <header class="sticky top-0 z-40 border-b border-ink-200 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-2 px-4 sm:gap-3 sm:px-6 lg:px-8">
            <a href="<?php echo e(route('catalog.index')); ?>" class="flex shrink-0 items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-lg bg-brand-600 text-white shadow-raise">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'store','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'store','class' => 'size-5']); ?>
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
                <span class="hidden text-sm font-bold tracking-tight text-ink-900 lg:block"><?php echo e(config('app.name')); ?></span>
            </a>

            <form action="<?php echo e(route('catalog.index')); ?>" method="GET" role="search" class="ml-auto min-w-0 flex-1 sm:ml-6 sm:max-w-md">
                <div class="relative">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'search','class' => 'pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','class' => 'pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-400']); ?>
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
                    <input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search goods or scan a barcode"
                           data-live-search class="input input-sm ps-9" aria-label="Search goods">
                </div>
            </form>

            <nav class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                <?php if (isset($component)) { $__componentOriginal658398a0e73a18931bb7def04d911f42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal658398a0e73a18931bb7def04d911f42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon-button','data' => ['icon' => 'heart','href' => route('favourites.index'),'count' => $favouriteCount,'label' => 'Favourites','active' => request()->routeIs('favourites.*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heart','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('favourites.index')),'count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($favouriteCount),'label' => 'Favourites','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('favourites.*'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal658398a0e73a18931bb7def04d911f42)): ?>
<?php $attributes = $__attributesOriginal658398a0e73a18931bb7def04d911f42; ?>
<?php unset($__attributesOriginal658398a0e73a18931bb7def04d911f42); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal658398a0e73a18931bb7def04d911f42)): ?>
<?php $component = $__componentOriginal658398a0e73a18931bb7def04d911f42; ?>
<?php unset($__componentOriginal658398a0e73a18931bb7def04d911f42); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal658398a0e73a18931bb7def04d911f42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal658398a0e73a18931bb7def04d911f42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon-button','data' => ['icon' => 'cart','href' => route('cart.index'),'count' => $cartCount,'label' => 'Cart']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'cart','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cart.index')),'count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cartCount),'label' => 'Cart']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal658398a0e73a18931bb7def04d911f42)): ?>
<?php $attributes = $__attributesOriginal658398a0e73a18931bb7def04d911f42; ?>
<?php unset($__attributesOriginal658398a0e73a18931bb7def04d911f42); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal658398a0e73a18931bb7def04d911f42)): ?>
<?php $component = $__componentOriginal658398a0e73a18931bb7def04d911f42; ?>
<?php unset($__componentOriginal658398a0e73a18931bb7def04d911f42); ?>
<?php endif; ?>

                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary btn-sm shrink-0 px-2.5 sm:px-3"
                       title="Staff dashboard" aria-label="Staff dashboard">
                        <span class="grid size-5 shrink-0 place-items-center">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'dashboard','class' => 'size-4.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'dashboard','class' => 'size-4.5']); ?>
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
                        <span class="hidden lg:inline">Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-secondary btn-sm shrink-0 px-2.5 sm:px-3">
                        <span class="grid size-5 shrink-0 place-items-center">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'logout','class' => 'size-4.5 rotate-180']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'logout','class' => 'size-4.5 rotate-180']); ?>
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
                        <span class="hidden sm:inline">Sign in</span>
                    </a>
                <?php endif; ?>
            </nav>

            <button type="button" class="btn-icon shrink-0 md:hidden" data-toggle="mobile-menu"
                    aria-controls="mobile-menu" aria-expanded="false" aria-label="Open classifications">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'menu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'menu']); ?>
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

        <div id="mobile-menu" class="hidden max-h-[70vh] overflow-y-auto border-t border-ink-200 bg-white px-4 py-3 md:hidden">
            <p class="section-title mb-2">Classifications</p>
            <ul class="space-y-0.5">
                <li>
                    <a href="<?php echo e(route('catalog.index')); ?>"
                       class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link', 'nav-link-active' => request()->routeIs('catalog.index')]); ?>">
                        All goods
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('catalog.new-arrivals')); ?>"
                       class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link', 'nav-link-active' => request()->routeIs('catalog.new-arrivals')]); ?>">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'sparkles','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'size-4']); ?>
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
                        New arrivals
                    </a>
                </li>
                <?php $__currentLoopData = $roots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $root): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e(route('catalog.show', $root)); ?>"
                           class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                               'nav-link',
                               // Only the deepest match lights up, so the drawer never
                               // marks both a department and its sub-classification.
                               'nav-link-active' => $activeRootSlug === $root->slug && ! $activeChildSlug,
                           ]); ?>">
                            <?php echo e($root->name); ?>

                        </a>
                        <?php if($root->children->isNotEmpty()): ?>
                            <ul class="ms-4 mt-0.5 space-y-0.5 border-s border-ink-200 ps-2">
                                <?php $__currentLoopData = $root->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('catalog.show', $child)); ?>"
                                           class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                               'nav-link py-2 text-[0.8125rem]',
                                               'nav-link-active' => $activeChildSlug === $child->slug,
                                           ]); ?>">
                                            <?php echo e($child->name); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>

        <nav class="hidden border-t border-ink-200 bg-white md:block">
            <div class="mx-auto flex max-w-7xl items-center gap-1 overflow-x-auto px-4 py-1.5 lg:px-8">
                <a href="<?php echo e(route('catalog.index')); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link !py-1.5 whitespace-nowrap', 'nav-link-active' => request()->routeIs('catalog.index')]); ?>">
                    All goods
                </a>
                <a href="<?php echo e(route('catalog.new-arrivals')); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'nav-link !py-1.5 whitespace-nowrap',
                       'nav-link-active' => request()->routeIs('catalog.new-arrivals'),
                   ]); ?>">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'sparkles','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'size-4']); ?>
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
                    New arrivals
                </a>
                <span class="mx-1 h-4 w-px shrink-0 bg-ink-200"></span>
                <?php $__currentLoopData = $roots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $root): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('catalog.show', $root)); ?>"
                       class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link !py-1.5 whitespace-nowrap', 'nav-link-active' => $activeRootSlug === $root->slug]); ?>">
                        <?php echo e($root->name); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </nav>
    </header>

    <main class="flex-1">
        <?php if (isset($component)) { $__componentOriginal5168fdb0c14fd91c6598264bc4be63f2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.flash','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2)): ?>
<?php $attributes = $__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2; ?>
<?php unset($__attributesOriginal5168fdb0c14fd91c6598264bc4be63f2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5168fdb0c14fd91c6598264bc4be63f2)): ?>
<?php $component = $__componentOriginal5168fdb0c14fd91c6598264bc4be63f2; ?>
<?php unset($__componentOriginal5168fdb0c14fd91c6598264bc4be63f2); ?>
<?php endif; ?>
        <?php echo e($slot); ?>

    </main>

    <footer class="mt-16 border-t border-ink-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-4 lg:px-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5">
                    <span class="grid size-9 place-items-center rounded-lg bg-brand-600 text-white">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'store','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'store','class' => 'size-5']); ?>
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
                    <span class="text-sm font-bold tracking-tight text-ink-900"><?php echo e(config('app.name')); ?></span>
                </div>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink-500">
                    Every item on the shelf, classified. Browse the full catalogue, check availability and keep
                    stock accurate in real time.
                </p>
            </div>

            <div>
                <p class="section-title mb-3">Classifications</p>
                <ul class="space-y-2 text-sm">
                    <?php $__currentLoopData = $roots->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $root): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e(route('catalog.show', $root)); ?>" class="text-ink-600 hover:text-brand-700"><?php echo e($root->name); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <div>
                <p class="section-title mb-3">Team</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="<?php echo e(route('admin.dashboard')); ?>" class="text-ink-600 hover:text-brand-700">Staff dashboard</a></li>
                    <li><a href="<?php echo e(route('login')); ?>" class="text-ink-600 hover:text-brand-700">Sign in</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-ink-200 px-4 py-5 text-center text-xs text-ink-400 sm:px-6 lg:px-8">
            &copy; <?php echo e(now()->year); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
        </div>
    </footer>
</div>
</body>
</html>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/layouts/public.blade.php ENDPATH**/ ?>