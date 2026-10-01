<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'heading' => null,
    'description' => null,
    'breadcrumbs' => [],
    'actions' => null,
    'wide' => false,
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
    'heading' => null,
    'description' => null,
    'breadcrumbs' => [],
    'actions' => null,
    'wide' => false,
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
    $nav = [
        [
            'label' => 'Overview',
            'items' => [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'permission' => null],
                ['route' => 'history.index', 'label' => 'My history', 'icon' => 'clock', 'permission' => null],
            ],
        ],
        [
            'label' => 'Catalogue',
            'items' => [
                ['route' => 'admin.products.index', 'label' => 'Goods', 'icon' => 'box', 'permission' => null],
                ['route' => 'admin.categories.index', 'label' => 'Classifications', 'icon' => 'layers', 'permission' => 'catalog'],
                ['route' => 'admin.suppliers.index', 'label' => 'Suppliers', 'icon' => 'truck', 'permission' => 'catalog'],
            ],
        ],
        [
            'label' => 'Operations',
            'items' => [
                ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => 'clipboard', 'permission' => null, 'pill' => 'orders'],
                ['route' => 'admin.notices.index', 'label' => 'Notices', 'icon' => 'bell', 'permission' => null],
                ['route' => 'admin.stock.index', 'label' => 'Stock movements', 'icon' => 'clipboard', 'permission' => null],
                ['route' => 'admin.stock.low', 'label' => 'Low stock alerts', 'icon' => 'alert', 'permission' => null, 'pill' => 'lowStock'],
            ],
        ],
        [
            'label' => 'Administration',
            'items' => [
                ['route' => 'admin.users.index', 'label' => 'Users & roles', 'icon' => 'users', 'permission' => 'users'],
                ['route' => 'admin.approvals.index', 'label' => 'New accounts', 'icon' => 'users', 'permission' => 'catalog', 'pill' => 'approvals'],
                ['route' => 'admin.activity.index', 'label' => 'Activity log', 'icon' => 'clock', 'permission' => 'catalog'],
            ],
        ],
    ];

$lowStockCount = \App\Models\Product::query()->lowStock()->count();
    // Orders this person has not cleared from the bell yet. The bell asks for the
    // same list, so the service only runs the query once per request.
    $openOrderCount = app(\App\Notifications\TeamAlertService::class)->unreadCount();
    // Registrations waiting for somebody to accept or turn them down.
    $pendingAccounts = $user?->canManageCatalog()
        ? \App\Models\User::query()->where('status', \App\Enums\AccountStatus::Pending)->count()
        : 0;
    $notifications = app(\App\Notifications\NotificationService::class);
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ? $title.' · ' : ''); ?><?php echo e(config('app.name')); ?></title>
    
    <?php if (isset($component)) { $__componentOriginald165ea9fefcd025b5d835007adfd5466 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald165ea9fefcd025b5d835007adfd5466 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.theme-script','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('theme-script'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald165ea9fefcd025b5d835007adfd5466)): ?>
<?php $attributes = $__attributesOriginald165ea9fefcd025b5d835007adfd5466; ?>
<?php unset($__attributesOriginald165ea9fefcd025b5d835007adfd5466); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald165ea9fefcd025b5d835007adfd5466)): ?>
<?php $component = $__componentOriginald165ea9fefcd025b5d835007adfd5466; ?>
<?php unset($__componentOriginald165ea9fefcd025b5d835007adfd5466); ?>
<?php endif; ?>
    <link rel="icon" href="/favicon.ico">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full">
<div class="flex min-h-full">

    <div id="sidebar-overlay" data-overlay-lock="true" data-toggle="sidebar-overlay!" class="fixed inset-0 z-40 hidden bg-ink-950/50 backdrop-blur-sm lg:hidden"></div>

    <aside id="sidebar" data-overlay-lock="true"
           class="fixed inset-y-0 left-0 z-50 hidden w-72 flex-col border-r border-ink-200 bg-surface lg:flex">
        <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-ink-200 px-5">
            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-600 text-white shadow-raise">
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
            <div class="min-w-0">
                <p class="truncate text-sm font-bold tracking-tight text-ink-900">Goods Hub</p>
                <p class="truncate text-[0.6875rem] text-ink-500">Inventory control</p>
            </div>
            <button type="button" class="btn-icon ms-auto lg:hidden" data-toggle="sidebar,sidebar-overlay!" aria-label="Close menu">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'x']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x']); ?>
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

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
            <?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $visible = collect($group['items'])->filter(function ($item) use ($user) {
                        return $item['permission'] === 'users' ? $user?->canManageUsers()
                            : ($item['permission'] === 'catalog' ? $user?->canManageCatalog() : true);
                    });
                ?>

                <?php if($visible->isNotEmpty()): ?>
                    <div>
                        <p class="section-title mb-2 px-3"><?php echo e($group['label']); ?></p>
                        <ul class="space-y-0.5">
                            <?php $__currentLoopData = $visible; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $active = request()->routeIs($item['route']); ?>
                                <li>
                                    <a href="<?php echo e(route($item['route'])); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['nav-link', 'nav-link-active' => $active]); ?>"
                                       <?php if($active): ?> aria-current="page" <?php endif; ?>>
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $item['icon'],'class' => 'size-[1.125rem] shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['icon']),'class' => 'size-[1.125rem] shrink-0']); ?>
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
                                        <span class="truncate"><?php echo e($item['label']); ?></span>
<?php if(($item['pill'] ?? null) === 'lowStock' && $lowStockCount > 0): ?>
                                            <span class="ms-auto rounded-full bg-rose-100 px-1.5 py-0.5 text-[0.625rem] font-bold text-rose-700 tabular-nums">
                                                <?php echo e($lowStockCount > 99 ? '99+' : $lowStockCount); ?>

                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if(($item['pill'] ?? null) === 'orders' && $openOrderCount > 0): ?>
                                            <span data-order-pill
                                                  class="ms-auto rounded-full bg-rose-100 px-1.5 py-0.5 text-[0.625rem] font-bold text-rose-700 tabular-nums">
                                                <?php echo e($openOrderCount > 99 ? '99+' : $openOrderCount); ?>

                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if(($item['pill'] ?? null) === 'approvals' && $pendingAccounts > 0): ?>
                                            <span data-approval-pill
                                                  class="ms-auto rounded-full bg-violet-100 px-1.5 py-0.5 text-[0.625rem] font-bold text-violet-700 tabular-nums">
                                                <?php echo e($pendingAccounts > 99 ? '99+' : $pendingAccounts); ?>

                                            </span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <div class="shrink-0 border-t border-ink-200 p-3">
            <a href="<?php echo e(route('catalog.index')); ?>" class="nav-link mb-2">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'cart','class' => 'size-[1.125rem] shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cart','class' => 'size-[1.125rem] shrink-0']); ?>
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
                <span>Public catalogue</span>
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','class' => 'ms-auto size-4 opacity-50']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'ms-auto size-4 opacity-50']); ?>
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
            <div class="flex items-center gap-3 rounded-lg bg-ink-50 p-2.5">
                <?php if($user?->avatarUrl()): ?>
                    <img src="<?php echo e($user->avatarUrl()); ?>" alt="" class="size-9 shrink-0 rounded-full object-cover ring-2 ring-white">
                <?php else: ?>
                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-600 text-xs font-bold text-white">
                        <?php echo e($user?->initials() ?? '—'); ?>

                    </span>
                <?php endif; ?>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-ink-900"><?php echo e($user?->name); ?></p>
                    <p class="truncate text-[0.6875rem] text-ink-500"><?php echo e($user?->role->label()); ?></p>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-icon" title="Sign out" aria-label="Sign out">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'logout','class' => 'size-[1.125rem]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'logout','class' => 'size-[1.125rem]']); ?>
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
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col lg:ps-72">
        <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-ink-200 bg-surface/85 px-4 backdrop-blur-md sm:px-6">
            <button type="button" class="btn-icon lg:hidden" data-toggle="sidebar,sidebar-overlay" aria-label="Open menu">
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

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-base font-bold tracking-tight text-ink-900 sm:text-lg">
                    <?php echo e($heading ?? $title ?? 'Dashboard'); ?>

                </h1>
                <?php if($description): ?>
                    <p class="truncate text-xs text-ink-500 sm:text-sm"><?php echo e($description); ?></p>
                <?php endif; ?>
            </div>

            <form action="<?php echo e(route('admin.products.index')); ?>" method="GET" role="search" class="hidden md:block md:w-72 lg:w-80">
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
                    <input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search goods, SKU, barcode…"
                           data-live-search
                           class="input input-sm ps-9" aria-label="Search goods">
                </div>
            </form>

            <?php if($actions): ?>
                <div class="flex shrink-0 items-center gap-2"><?php echo $actions; ?></div>
            <?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginale5bc9b34dd139a393f71cdc403b71855 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5bc9b34dd139a393f71cdc403b71855 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notifications','data' => ['team' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notifications'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['team' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5bc9b34dd139a393f71cdc403b71855)): ?>
<?php $attributes = $__attributesOriginale5bc9b34dd139a393f71cdc403b71855; ?>
<?php unset($__attributesOriginale5bc9b34dd139a393f71cdc403b71855); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5bc9b34dd139a393f71cdc403b71855)): ?>
<?php $component = $__componentOriginale5bc9b34dd139a393f71cdc403b71855; ?>
<?php unset($__componentOriginale5bc9b34dd139a393f71cdc403b71855); ?>
<?php endif; ?>
        </header>

        <main class="flex-1 px-4 py-5 sm:px-6 sm:py-6">
            <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
                <div class="mb-4">
                    <?php echo $__env->yieldContent('breadcrumb'); ?>
                </div>
            <?php endif; ?>

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
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>