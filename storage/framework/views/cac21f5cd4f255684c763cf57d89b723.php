<?php
    // Amounts come from the app's own money helper, so a department total is
    // printed in the shop's currency rather than a symbol typed in here.
    $chartFloor = 6;
?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php if (isset($component)) { $__componentOriginal360d002b1b676b6f84d43220f22129e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal360d002b1b676b6f84d43220f22129e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumbs','data' => ['items' => [['label' => 'Dashboard']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['label' => 'Dashboard']])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $attributes = $__attributesOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__attributesOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $component = $__componentOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__componentOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Dashboard','heading' => 'Dashboard','description' => 'How the store is doing right now']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard','heading' => 'Dashboard','description' => 'How the store is doing right now']); ?>
     <?php $__env->slot('actions', null, []); ?> 
        
        <?php if($waitingAccounts->isNotEmpty()): ?>
            <a href="<?php echo e(route('admin.approvals.index')); ?>" class="btn btn-primary btn-sm">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'users','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'users','class' => 'size-4']); ?>
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
                <?php echo e($waitingAccounts->count() === 1 ? '1 new account' : $waitingAccounts->count().' new accounts'); ?>

            </a>
        <?php endif; ?>

        <div data-live-region data-live-interval="60" class="hidden items-center gap-2 sm:flex">
            <span class="relative flex size-2">
                <span class="absolute inline-flex size-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                <span class="relative inline-flex size-2 rounded-full bg-brand-500"></span>
            </span>
            <span data-live-stamp class="text-xs text-ink-500 tabular-nums">just now</span>
        </div>

        <button type="button" data-autorefresh class="btn btn-secondary btn-sm" title="Reload every 60 seconds">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'refresh','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'refresh','class' => 'size-4']); ?>
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
            <span class="hidden sm:inline" data-autorefresh-label>Auto-refresh off</span>
        </button>

        <?php if(auth()->user()->canManageCatalog()): ?>
            <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary btn-sm">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'size-4']); ?>
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
                <span class="hidden sm:inline">Add goods</span>
            </a>
        <?php endif; ?>
     <?php $__env->endSlot(); ?>

    <div class="space-y-4">

        
        <?php if($waitingAccounts->isNotEmpty()): ?>
            <section class="card overflow-hidden ring-1 ring-inset ring-violet-600/20">
                <div class="card-header bg-violet-50/60 dark:bg-violet-950/20">
                    <div class="flex min-w-0 items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'users','class' => 'size-4 shrink-0 text-violet-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'users','class' => 'size-4 shrink-0 text-violet-600']); ?>
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
                        <h2 class="card-title">Accounts waiting to be accepted</h2>
                    </div>
                    <a href="<?php echo e(route('admin.approvals.index')); ?>" class="btn btn-primary btn-sm shrink-0">
                        Open the queue
                    </a>
                </div>

                <div class="divide-y divide-ink-100 dark:divide-ink-200">
                    <?php $__currentLoopData = $waitingAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex flex-wrap items-center gap-3 p-3.5">
                            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-violet-100 text-xs font-bold text-violet-700">
                                <?php echo e($person->initials()); ?>

                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-ink-900"><?php echo e($person->name); ?></p>
                                <p class="truncate text-xs text-ink-500">
                                    <?php echo e($person->email); ?> · registered <?php echo e($person->created_at?->diffForHumans() ?? 'just now'); ?>

                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-1.5">
                                
                                <form method="POST" action="<?php echo e(route('admin.approvals.accept', $person)); ?>"
                                      data-confirm="Accept <?php echo e($person->name); ?> as staff? They will be able to sign in at the staff door.">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-primary btn-sm"
                                            title="Accept: <?php echo e($person->name); ?> becomes staff and can sign in">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'size-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Accept as staff
                                    </button>
                                </form>

                                <form method="POST" action="<?php echo e(route('admin.approvals.reject', $person)); ?>"
                                      data-confirm="Turn down <?php echo e($person->name); ?> for good? They will never be able to sign in.">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-secondary btn-sm text-rose-600 hover:bg-rose-50"
                                            title="Reject: <?php echo e($person->name); ?> can never sign in">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'x','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x','class' => 'size-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <p class="border-t border-ink-100 bg-ink-50 px-4 py-2.5 text-xs leading-relaxed text-ink-600 dark:border-ink-200 dark:bg-ink-100 dark:text-ink-300">
                    <strong class="font-semibold text-ink-800 dark:text-ink-100">Accept</strong> makes the account a
                    member of staff, able to sign in and reach goods, stock and orders.
                    <strong class="font-semibold text-ink-800 dark:text-ink-100">Reject</strong> closes it permanently
                    &#8212; they can never sign in and that address cannot register again.
                </p>
            </section>
        <?php endif; ?>

        
        <section class="card overflow-hidden">
            <div class="grid gap-5 p-5 sm:p-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-center">
                <div>
                    <div class="flex items-start gap-3">
                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'mt-0.5 grid size-11 shrink-0 place-items-center rounded-xl ring-1 ring-inset',
                            'bg-emerald-50 text-emerald-600 ring-emerald-600/20' => $health['score'] >= 90,
                            'bg-amber-50 text-amber-600 ring-amber-600/20' => $health['score'] >= 70 && $health['score'] < 90,
                            'bg-rose-50 text-rose-600 ring-rose-600/20' => $health['score'] < 70,
                        ]); ?>">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $health['score'] >= 90 ? 'check' : 'alert','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($health['score'] >= 90 ? 'check' : 'alert'),'class' => 'size-6']); ?>
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
                            <p class="section-title">Shelf health</p>
                            <h2 class="mt-0.5 text-lg font-bold tracking-tight text-ink-900 sm:text-xl">
                                <?php echo e($health['headline']); ?>

                            </h2>
                            <p class="mt-1 text-sm text-ink-500">
                                <strong class="font-semibold text-ink-800 tabular-nums"><?php echo e(number_format($health['score'])); ?>%</strong>
                                of <?php echo e(number_format($health['total'])); ?> goods items are above their reorder threshold.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex h-3 w-full overflow-hidden rounded-full bg-ink-100">
                        <?php $__currentLoopData = $health['segments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($segment['percent'] > 0): ?>
                                <a href="<?php echo e($segment['key'] === 'ok' ? route('admin.products.index', ['status' => 'active']) : route('admin.stock.low')); ?>"
                                   title="<?php echo e($segment['label']); ?>: <?php echo e($segment['count']); ?> (<?php echo e($segment['percent']); ?>%)"
                                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                       'bar-animate h-full transition-colors',
                                       'bg-emerald-500 hover:bg-emerald-600' => $segment['key'] === 'ok',
                                       'bg-amber-400 hover:bg-amber-500' => $segment['key'] === 'low',
                                       'bg-rose-500 hover:bg-rose-600' => $segment['key'] === 'out',
                                   ]); ?>"
                                   style="--w: <?php echo e(max(1.5, $segment['percent'])); ?>%"></a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2">
                        <?php $__currentLoopData = $health['segments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($segment['key'] === 'ok' ? route('admin.products.index', ['status' => 'active']) : route('admin.stock.low')); ?>"
                               class="group flex items-center gap-2 text-xs">
                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'size-2.5 rounded-full',
                                    'bg-emerald-500' => $segment['key'] === 'ok',
                                    'bg-amber-400' => $segment['key'] === 'low',
                                    'bg-rose-500' => $segment['key'] === 'out',
                                ]); ?>"></span>
                                <span class="text-ink-500 group-hover:text-ink-800"><?php echo e($segment['label']); ?></span>
                                <span class="font-semibold text-ink-900 tabular-nums"><?php echo e(number_format($segment['count'])); ?></span>
                                <span class="text-ink-400 tabular-nums">(<?php echo e($segment['percent']); ?>%)</span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <a href="<?php echo e(route('admin.stock.low')); ?>"
                   class="flex items-center justify-between gap-4 rounded-xl border border-ink-200 bg-ink-50/70 p-4 transition hover:border-rose-300 hover:bg-rose-50/50">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase">Action needed</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-ink-900 tabular-nums">
                            <?php echo e(number_format($health['needsAction'])); ?>

                            <span class="text-sm font-medium text-ink-500">items</span>
                        </p>
                    </div>
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-rose-600 text-white shadow-raise">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'size-5']); ?>
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
                </a>
            </div>
        </section>

        
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($kpi['link']); ?>" class="card group p-4 transition hover:-translate-y-0.5 hover:shadow-raise sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase"><?php echo e($kpi['label']); ?></p>
                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'grid size-9 shrink-0 place-items-center rounded-lg ring-1 ring-inset transition group-hover:scale-105',
                            'bg-brand-50 text-brand-600 ring-brand-600/15' => $kpi['tone'] === 'brand',
                            'bg-emerald-50 text-emerald-600 ring-emerald-600/15' => $kpi['tone'] === 'emerald',
                            'bg-sky-50 text-sky-600 ring-sky-600/15' => $kpi['tone'] === 'sky',
                            'bg-violet-50 text-violet-600 ring-violet-600/15' => $kpi['tone'] === 'violet',
                        ]); ?>">
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $kpi['icon'],'class' => 'size-[1.125rem]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpi['icon']),'class' => 'size-[1.125rem]']); ?>
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

                    <p class="mt-2 flex items-baseline gap-1.5">
                        <span <?php if(! empty($kpi['money'])): ?>
                            data-count-to="<?php echo e((float) $kpi['value']); ?>" data-count-money
                        <?php else: ?>
                            data-count-to="<?php echo e($kpi['value']); ?>"
                        <?php endif; ?>
                              class="text-[1.75rem] leading-none font-bold tracking-tight text-ink-900 tabular-nums">0</span>
                        <?php if(! empty($kpi['unit'])): ?>
                            <span class="text-sm font-medium text-ink-400"><?php echo e($kpi['unit']); ?></span>
                        <?php endif; ?>
                    </p>

                    <p class="mt-2 truncate text-xs text-ink-500"><?php echo e($kpi['hint']); ?></p>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>

        <div class="grid items-start gap-4 xl:grid-cols-3">

            
            <section class="card overflow-hidden xl:col-span-2">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Restock queue</h2>
                        <p class="text-xs text-ink-400">Most urgent first · <?php echo e(count($restock)); ?> shown</p>
                    </div>
                    <a href="<?php echo e(route('admin.stock.low')); ?>" class="btn btn-secondary btn-sm">
                        Open list
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'size-4']); ?>
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
                </div>

                <?php if($restock->isEmpty()): ?>
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'check','title' => 'Nothing to reorder','description' => 'Every goods item is above its reorder threshold.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'check','title' => 'Nothing to reorder','description' => 'Every goods item is above its reorder threshold.']); ?>
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
                    <ul class="divide-y divide-ink-100">
                        <?php $__currentLoopData = $restock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $product = $row['product']; ?>
                            <li class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-brand-50/40 sm:gap-4 sm:px-5">
                                <?php if($product->imageUrl()): ?>
                                    <img src="<?php echo e($product->imageUrl()); ?>" alt="" class="size-10 shrink-0 rounded-lg object-cover ring-1 ring-ink-200">
                                <?php else: ?>
                                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ink-100 text-ink-400">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'box','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'box','class' => 'size-5']); ?>
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
                                    <p class="truncate text-sm font-semibold text-ink-900"><?php echo e($product->name); ?></p>
                                    <p class="truncate text-[0.6875rem] text-ink-400">
                                        <?php echo e($product->sku); ?>

                                        <?php if($product->category): ?> · <?php echo e($product->category->name); ?> <?php endif; ?>
                                    </p>
                                </div>

                                <div class="hidden w-44 shrink-0 sm:block">
                                    <div class="mb-1 flex items-baseline justify-between gap-2 text-[0.6875rem]">
                                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'font-semibold',
                                            'text-rose-600' => $row['tone'] === 'rose',
                                            'text-amber-600' => $row['tone'] === 'amber',
                                        ]); ?>"><?php echo e($product->stock); ?> left</span>
                                        <span class="text-ink-400 tabular-nums">min <?php echo e($product->min_stock); ?></span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-ink-100">
                                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'bar-animate h-full rounded-full',
                                            'bg-rose-500' => $row['tone'] === 'rose',
                                            'bg-amber-400' => $row['tone'] === 'amber',
                                        ]); ?>" style="--w: <?php echo e($row['ratio']); ?>%"></div>
                                    </div>
                                </div>

                                <div class="hidden shrink-0 text-right lg:block">
                                    <p class="text-[0.6875rem] text-ink-400">Order</p>
                                    <p class="text-sm font-semibold text-ink-800 tabular-nums">+<?php echo e($row['suggested']); ?> <?php echo e($product->unit); ?></p>
                                </div>

                                <a href="<?php echo e(route('admin.products.edit', $product)); ?>"
                                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                       'btn btn-sm shrink-0',
                                       'btn-danger' => $row['tone'] === 'rose',
                                       'btn-soft' => $row['tone'] === 'amber',
                                   ]); ?>">
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-down','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-down','class' => 'size-3.5']); ?>
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
                                    <span class="hidden md:inline">Restock</span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </section>

            
            <section class="card flex flex-col overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Goods movement</h2>
                        <p class="text-xs text-ink-400">Last 30 days</p>
                    </div>
                    <a href="<?php echo e(route('admin.stock.index')); ?>" class="btn btn-ghost btn-sm" title="Full log">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'size-4']); ?>
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
                </div>

                <div class="flex items-center gap-4 px-4 pt-1 sm:px-5">
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-sm bg-emerald-500"></span>
                        <span class="text-xs text-ink-500">In</span>
                        <span class="text-sm font-bold text-ink-900 tabular-nums"><?php echo e(number_format($movement['in'])); ?></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-sm bg-rose-400"></span>
                        <span class="text-xs text-ink-500">Out</span>
                        <span class="text-sm font-bold text-ink-900 tabular-nums"><?php echo e(number_format($movement['out'])); ?></span>
                    </div>
                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'badge ms-auto',
                        'bg-emerald-50 text-emerald-700 ring-emerald-600/20' => $movement['net'] >= 0,
                        'bg-rose-50 text-rose-700 ring-rose-600/20' => $movement['net'] < 0,
                    ]); ?>">
                        <?php echo e($movement['net'] >= 0 ? '+' : ''); ?><?php echo e(number_format($movement['net'])); ?> net
                    </span>
                </div>

                <div class="mt-4 flex-1 px-4 pb-4 sm:px-5">
                    <div class="flex h-40 items-end gap-[3px]" role="img"
                         aria-label="Daily goods in and out for the last <?php echo e(count($movement['days'])); ?> days">
                        <?php $__currentLoopData = $movement['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $inH = round($day['in'] / $movement['peak'] * 100, 1);
                                $outH = round($day['out'] / $movement['peak'] * 100, 1);
                                $empty = $day['total'] === 0;
                            ?>
                            <div class="group relative flex h-full flex-1 flex-col justify-end gap-[2px]"
                                 title="<?php echo e($day['label']); ?> · in <?php echo e($day['in']); ?> · out <?php echo e($day['out']); ?>">
                                <?php if (! ($empty)): ?>
                                    <?php if($day['out'] > 0): ?>
                                        <div class="bar-animate w-full rounded-t-sm bg-rose-400 transition-colors group-hover:bg-rose-500"
                                             style="--w: <?php echo e(max(3, $outH)); ?>%; height: <?php echo e($outH); ?>%"></div>
                                    <?php endif; ?>
                                    <?php if($day['in'] > 0): ?>
                                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'bar-animate w-full bg-emerald-500 transition-colors group-hover:bg-emerald-600',
                                            'rounded-t-sm' => $day['out'] > 0,
                                            'rounded-sm' => $day['out'] === 0,
                                        ]); ?>" style="--w: 100%; height: <?php echo e($inH); ?>%"></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="h-[3px] w-full rounded-sm bg-ink-200/70"></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mt-2 flex justify-between text-[0.625rem] text-ink-400">
                        <span><?php echo e($movement['days'][0]['label']); ?></span>
                        <span><?php echo e($movement['days'][count($movement['days']) - 1]['label']); ?></span>
                    </div>
                </div>

                <div class="border-t border-ink-200 px-4 py-3 sm:px-5">
                    <p class="text-xs text-ink-500">
                        Recorded on <strong class="font-semibold text-ink-800 tabular-nums"><?php echo e($movement['activeDays']); ?></strong>
                        of the last <?php echo e(count($movement['days'])); ?> days.
                    </p>
                </div>
            </section>
        </div>

        <div class="grid items-start gap-4 xl:grid-cols-3">
            <div class="space-y-4 xl:col-span-2">

                
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Where your money sits</h2>
                            <p class="text-xs text-ink-400">Stock value by department, aisles included</p>
                        </div>
                        <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-ghost btn-sm">
                            Classifications
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'size-4']); ?>
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
                    </div>

                    <?php if($departments->isEmpty()): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'layers','title' => 'No classifications yet','description' => 'Create a department to start organising your goods.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'layers','title' => 'No classifications yet','description' => 'Create a department to start organising your goods.']); ?>
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
                        <div class="card-body space-y-3.5">
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="mb-1.5 flex items-baseline justify-between gap-3">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <p class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'truncate text-sm font-semibold',
                                                'text-ink-500' => $dept['isOther'],
                                                'text-ink-800' => ! $dept['isOther'],
                                            ]); ?>"><?php echo e($dept['name']); ?></p>
                                            <span class="shrink-0 text-[0.6875rem] text-ink-400 tabular-nums"><?php echo e($dept['items']); ?> items</span>
                                        </div>
                                        <p class="shrink-0 text-xs font-semibold text-ink-700 tabular-nums">
                                            <?php echo e(\App\Support\Money::format($dept['value'])); ?>

                                            <span class="font-normal text-ink-400"><?php echo e($dept['share']); ?>%</span>
                                        </p>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-ink-100">
                                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'bar-animate h-full rounded-full transition-colors',
                                            'bg-ink-300' => $dept['isOther'],
                                            'bg-brand-500' => ! $dept['isOther'],
                                        ]); ?>" style="--w: <?php echo e(max(1.5, $dept['share'] * 3)); ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </section>

                
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Fastest movers</h2>
                            <p class="text-xs text-ink-400">Most goods handled in the last 30 days</p>
                        </div>
                        <?php if($movers['total'] > 0): ?>
                            <span class="text-xs text-ink-400 tabular-nums">
                                <?php echo e(number_format($movers['total'])); ?> units total
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if($movers['items']->isEmpty()): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'clipboard','title' => 'No movements yet','description' => 'Record goods in or out to see what moves fastest.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'clipboard','title' => 'No movements yet','description' => 'Record goods in or out to see what moves fastest.']); ?>
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
                        <ol class="divide-y divide-ink-100">
                            <?php $__currentLoopData = $movers['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $product = $row['product'];
                                    $share = $movers['total'] > 0 ? round($row['total'] / $movers['total'] * 100, 1) : 0;
                                ?>
                                <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                                    <span class="grid size-6 shrink-0 place-items-center rounded-md bg-ink-100 text-[0.6875rem] font-bold text-ink-500 tabular-nums">
                                        <?php echo e($index + 1); ?>

                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <a href="<?php echo e(route('admin.products.edit', $product)); ?>"
                                           class="block truncate text-sm font-medium text-ink-800 hover:text-brand-700">
                                            <?php echo e($product->name); ?>

                                        </a>
                                        <div class="mt-1 flex items-center gap-2">
                                            <div class="h-1 min-w-0 flex-1 overflow-hidden rounded-full bg-ink-100">
                                                <div class="bar-animate h-full rounded-full bg-brand-400"
                                                     style="--w: <?php echo e(max(2, $share * 3)); ?>%"></div>
                                            </div>
                                            <span class="shrink-0 text-[0.6875rem] text-ink-400 tabular-nums"><?php echo e($share); ?>%</span>
                                        </div>
                                    </div>
                                    <span class="w-16 shrink-0 text-right text-sm font-bold text-ink-900 tabular-nums">
                                        <?php echo e(number_format($row['total'])); ?>

                                        <span class="block text-[0.625rem] font-normal text-ink-400"><?php echo e($product->unit); ?></span>
                                    </span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ol>
                    <?php endif; ?>
                </section>
            </div>

            <div class="space-y-4">
                
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <h2 class="card-title">What changed</h2>
                        <a href="<?php echo e(route('admin.activity.index')); ?>" class="btn btn-ghost btn-sm">All</a>
                    </div>

                    <?php if($activity->isEmpty()): ?>
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'clock','title' => 'No changes yet','description' => 'Edits, deletions and stock movements will show up here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'clock','title' => 'No changes yet','description' => 'Edits, deletions and stock movements will show up here.']); ?>
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
                        <ul class="divide-y divide-ink-100">
                            <?php $__currentLoopData = $activity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-start gap-2.5 px-4 py-2.5 sm:px-5">
                                    <span class="badge <?php echo e($log->tone()); ?> mt-0.5 shrink-0"><?php echo e($log->action); ?></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs leading-snug text-ink-700"><?php echo e($log->description); ?></p>
                                        <p class="mt-0.5 text-[0.6875rem] text-ink-400">
                                            <?php echo e($log->user?->name ?? 'System'); ?> · <?php echo e($log->created_at->diffForHumans(short: true)); ?>

                                        </p>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </section>

                
                <section class="card overflow-hidden">
                    <div class="card-header">
                        <h2 class="card-title">On shift</h2>
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-ghost btn-sm">Team</a>
                    </div>
                    <ul class="divide-y divide-ink-100">
                        <?php $__currentLoopData = $team; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                                <span class="grid size-8 shrink-0 place-items-center rounded-full bg-brand-100 text-[0.6875rem] font-bold text-brand-700">
                                    <?php echo e($member->initials()); ?>

                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-ink-800"><?php echo e($member->name); ?></p>
                                    <p class="truncate text-[0.6875rem] text-ink-400"><?php echo e($member->role->label()); ?></p>
                                </div>
                                <span class="shrink-0 text-[0.6875rem] text-ink-400">
                                    <?php echo e($member->last_login_at ? $member->last_login_at->diffForHumans(short: true) : 'never'); ?>

                                </span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </section>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>