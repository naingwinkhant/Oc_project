<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => 'Your account','description' => 'Your orders and your details']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Your account','description' => 'Your orders and your details']); ?>
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <?php if (isset($component)) { $__componentOriginal8026f1991abb42645b4d7cc7ace47942 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8026f1991abb42645b4d7cc7ace47942 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-heading','data' => ['icon' => 'user','title' => 'Hello '.e(Str::before($user->name, ' ')).'','lede' => 'Ordering never needs an account. This is just where you can see what you have already bought.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'user','title' => 'Hello '.e(Str::before($user->name, ' ')).'','lede' => 'Ordering never needs an account. This is just where you can see what you have already bought.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8026f1991abb42645b4d7cc7ace47942)): ?>
<?php $attributes = $__attributesOriginal8026f1991abb42645b4d7cc7ace47942; ?>
<?php unset($__attributesOriginal8026f1991abb42645b4d7cc7ace47942); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8026f1991abb42645b4d7cc7ace47942)): ?>
<?php $component = $__componentOriginal8026f1991abb42645b4d7cc7ace47942; ?>
<?php unset($__componentOriginal8026f1991abb42645b4d7cc7ace47942); ?>
<?php endif; ?>

        <div class="mt-6 space-y-5">
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Your details</h2>
                    <span class="badge <?php echo e($user->role->color()); ?>"><?php echo e($user->role->label()); ?></span>
                </div>
                <div class="card-body grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Name</p>
                        <p class="mt-0.5 text-sm text-ink-900"><?php echo e($user->name); ?></p>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Email</p>
                        <p class="mt-0.5 text-sm text-ink-900"><?php echo e($user->email); ?></p>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Username</p>
                        <p class="mt-0.5 text-sm text-ink-900"><?php echo e($user->username); ?></p>
                    </div>
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Account</p>
                        <p class="mt-0.5">
                            <span class="badge <?php echo e($user->status->tone()); ?>"><?php echo e($user->status->label()); ?></span>
                        </p>
                    </div>
                </div>
            </section>

            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Your orders</h2>
                    <span class="text-xs text-ink-400"><?php echo e($orders->count()); ?> shown, matched on <?php echo e($user->email); ?></span>
                </div>

                <?php if($orders->isEmpty()): ?>
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'clipboard','title' => 'No orders yet','description' => 'Anything you order with this email address will be listed here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'clipboard','title' => 'No orders yet','description' => 'Anything you order with this email address will be listed here.']); ?>
                         <?php $__env->slot('action', null, []); ?> 
                            <a href="<?php echo e(route('catalog.index')); ?>" class="btn btn-primary btn-sm">Browse the goods</a>
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
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th class="hidden sm:table-cell">Items</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th class="hidden text-end lg:table-cell">Placed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('checkout.show', $order)); ?>"
                                               class="font-mono text-xs font-semibold text-ink-900 hover:text-brand-700">
                                                <?php echo e($order->order_number); ?>

                                            </a>
                                            <p class="text-[0.6875rem] text-ink-400"><?php echo e($order->township); ?></p>
                                        </td>
                                        <td class="hidden sm:table-cell text-sm tabular-nums text-ink-600">
                                            <?php echo e($order->itemCount()); ?>

                                        </td>
                                        <td class="text-end text-sm font-semibold tabular-nums text-ink-900">
                                            <?php echo e($order->totalFormatted()); ?>

                                        </td>
                                        <td>
                                            <span class="badge <?php echo e($order->status->tone()); ?>"><?php echo e($order->status->label()); ?></span>
                                        </td>
                                        <td class="hidden text-right text-xs text-ink-400 lg:table-cell">
                                            <?php echo e($order->placed_at?->diffForHumans() ?? '—'); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('catalog.index')); ?>" class="btn btn-primary">
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
<?php endif; ?> Continue shopping
                </a>
                <a href="<?php echo e(route('settings')); ?>" class="btn btn-secondary">Settings</a>

                <form method="POST" action="<?php echo e(route('logout')); ?>" class="ms-auto">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-ghost text-rose-600 hover:bg-rose-50">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'logout','class' => 'size-4 rotate-180']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'logout','class' => 'size-4 rotate-180']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Sign out
                    </button>
                </form>
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
<?php endif; ?><?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/account/index.blade.php ENDPATH**/ ?>