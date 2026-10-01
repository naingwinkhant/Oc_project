<?php $__env->startSection('breadcrumb'); ?>
    <?php if (isset($component)) { $__componentOriginal360d002b1b676b6f84d43220f22129e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal360d002b1b676b6f84d43220f22129e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumbs','data' => ['items' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Orders', 'url' => route('admin.orders.index')],
        ['label' => $order->order_number],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Orders', 'url' => route('admin.orders.index')],
        ['label' => $order->order_number],
    ])]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Order '.$order->order_number,'heading' => 'Order details','description' => $order->customer_name.' · '.$order->placed_at?->format('j M Y, g:i A')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Order '.$order->order_number),'heading' => 'Order details','description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order->customer_name.' · '.$order->placed_at?->format('j M Y, g:i A'))]); ?>
     <?php $__env->slot('actions', null, []); ?> 
        
        <?php if(auth()->user()?->canManageCatalog()): ?>
            <?php if (! ($order->status->isClosed())): ?>
                <a href="<?php echo e(route('admin.orders.edit', $order)); ?>" class="btn btn-secondary btn-sm">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'pencil','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'pencil','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Edit details
                </a>
            <?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalec2502b834f860c8e30d229aa8f280e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalec2502b834f860c8e30d229aa8f280e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-button','data' => ['name' => 'Delete order','icon' => 'trash','action' => route('admin.orders.destroy', $order),'confirm' => 'Delete order '.$order->order_number.'? This cannot be undone.','class' => 'btn btn-secondary btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Delete order','icon' => 'trash','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.orders.destroy', $order)),'confirm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Delete order '.$order->order_number.'? This cannot be undone.'),'class' => 'btn btn-secondary btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalec2502b834f860c8e30d229aa8f280e2)): ?>
<?php $attributes = $__attributesOriginalec2502b834f860c8e30d229aa8f280e2; ?>
<?php unset($__attributesOriginalec2502b834f860c8e30d229aa8f280e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalec2502b834f860c8e30d229aa8f280e2)): ?>
<?php $component = $__componentOriginalec2502b834f860c8e30d229aa8f280e2; ?>
<?php unset($__componentOriginalec2502b834f860c8e30d229aa8f280e2); ?>
<?php endif; ?>
        <?php endif; ?>
     <?php $__env->endSlot(); ?>

    <div class="grid items-start gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Items</h2>
                    <span class="text-xs text-ink-400"><?php echo e($order->itemCount()); ?> <?php echo e(Str::plural('unit', $order->itemCount())); ?></span>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Goods</th>
                                <th class="text-end">Unit price</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Line total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <p class="text-sm font-semibold text-ink-900"><?php echo e($item->name); ?></p>
                                        <p class="font-mono text-[0.6875rem] text-ink-400"><?php echo e($item->sku); ?></p>
                                        <?php if($item->product): ?>
                                            <?php if (isset($component)) { $__componentOriginal55a2a145c73971d8b3899d9083ae76a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55a2a145c73971d8b3899d9083ae76a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.freshness','data' => ['product' => $item->product,'variant' => 'compact']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('freshness'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->product),'variant' => 'compact']); ?>
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
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-sm tabular-nums"><?php echo e($item->unitPriceFormatted()); ?></td>
                                    <td class="text-end text-sm tabular-nums"><?php echo e($item->quantity); ?> <?php echo e($item->unit); ?></td>
                                    <td class="text-end text-sm font-semibold tabular-nums"><?php echo e($item->lineTotalFormatted()); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="space-y-2 border-t border-ink-200 p-4 sm:p-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-600">Subtotal</span>
                        <span class="font-semibold tabular-nums"><?php echo e(\App\Support\Money::format($order->subtotal)); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-600">Delivery</span>
                        <span class="font-semibold tabular-nums"><?php echo e(\App\Support\Money::format($order->delivery_fee)); ?></span>
                    </div>
                    <div class="flex justify-between border-t border-ink-200 pt-2 text-base font-bold">
                        <span>Total</span>
                        <span class="tabular-nums"><?php echo e($order->totalFormatted()); ?></span>
                    </div>
                </div>
            </section>

            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Payment attempts</h2>
                </div>
                <?php if($order->payments->isEmpty()): ?>
                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'credit','title' => 'No payment recorded']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'credit','title' => 'No payment recorded']); ?>
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
                                    <th>Gateway</th>
                                    <th class="hidden sm:table-cell">Reference</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                    <th class="hidden lg:table-cell text-end">When</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $order->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <span class="badge <?php echo e($payment->gateway->tone()); ?>"><?php echo e($payment->gateway->label()); ?></span>
                                        </td>
                                        <td class="hidden font-mono text-xs text-ink-500 sm:table-cell">
                                            <?php echo e($payment->gateway_reference ?? '—'); ?>

                                        </td>
                                        <td class="text-end text-sm tabular-nums"><?php echo e($payment->amountFormatted()); ?></td>
                                        <td>
                                            <span class="badge <?php echo e($payment->status->tone()); ?>"><?php echo e($payment->status->label()); ?></span>
                                        </td>
                                        <td class="hidden whitespace-nowrap text-right text-xs text-ink-400 lg:table-cell">
                                            <?php echo e($payment->created_at->diffForHumans()); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <div class="space-y-5">
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Status</h2>
                    <span class="badge <?php echo e($order->status->tone()); ?>"><?php echo e($order->status->label()); ?></span>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <?php if($order->paid_at): ?>
                        <p class="text-xs text-emerald-700">Paid <?php echo e($order->paid_at->diffForHumans()); ?></p>
                    <?php endif; ?>

                    
                    <?php $__currentLoopData = $order->status->options(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <form method="POST" action="<?php echo e(route('admin.orders.status', $order)); ?>"
                              <?php if($option === \App\Enums\OrderStatus::Cancelled): ?> onsubmit="return confirm('Cancel this order?')" <?php endif; ?>>
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="status" value="<?php echo e($option->value); ?>">
                            <button type="submit"
                                    class="btn btn-sm w-full <?php echo e($option === \App\Enums\OrderStatus::Completed ? 'btn-primary' : 'btn-secondary'); ?> <?php echo e($option === \App\Enums\OrderStatus::Cancelled ? 'text-rose-600 hover:bg-rose-50' : ''); ?>">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $option === \App\Enums\OrderStatus::Completed ? 'check' : ($option === \App\Enums\OrderStatus::Refunded ? 'refresh' : 'x'),'class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($option === \App\Enums\OrderStatus::Completed ? 'check' : ($option === \App\Enums\OrderStatus::Refunded ? 'refresh' : 'x')),'class' => 'size-3.5']); ?>
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
                                <?php echo e($option === \App\Enums\OrderStatus::Completed ? 'Mark completed' : ($option === \App\Enums\OrderStatus::Refunded ? 'Mark refunded' : 'Cancel order')); ?>

                            </button>
                        </form>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if($order->status->options() === []): ?>
                        <p class="text-xs text-ink-500">
                            This order is closed, so there is nothing left to change.
                        </p>
                    <?php endif; ?>

                    <div class="border-t border-ink-100 pt-3">
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Payment method</p>
                        <?php if($order->payment_gateway): ?>
                            <span class="badge mt-1 <?php echo e($order->payment_gateway->tone()); ?>"><?php echo e($order->payment_gateway->label()); ?></span>
                        <?php else: ?>
                            <p class="mt-1 text-ink-400">—</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Customer</h2>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div>
                        <p class="font-medium text-ink-900"><?php echo e($order->customer_name); ?></p>
                        <?php if($order->user): ?>
                            <p class="text-xs text-ink-400">
                                &#64;<?php echo e($order->user->username); ?> · <?php echo e($order->user->name); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="border-t border-ink-100 pt-3 text-ink-600">
                        <p><?php echo e($order->phone); ?></p>
                        <p><?php echo e($order->email); ?></p>
                    </div>
                    <div class="border-t border-ink-100 pt-3 text-ink-600">
                        <p class="text-xs leading-relaxed"><?php echo e($order->delivery_address); ?></p>
                        <p class="mt-0.5 text-xs"><?php echo e($order->township); ?></p>
                    </div>
                    <?php if($order->note): ?>
                        <div class="border-t border-ink-100 pt-3">
                            <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Note</p>
                            <p class="mt-0.5 text-xs text-ink-600"><?php echo e($order->note); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
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
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>