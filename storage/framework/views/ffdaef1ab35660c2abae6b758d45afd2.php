<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => 'Order '.$order->order_number,'description' => 'Order confirmation and payment']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Order '.$order->order_number),'description' => 'Order confirmation and payment']); ?>
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <div class="mb-6 text-center">
            <?php if($order->isPaid()): ?>
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-600/20">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'size-7']); ?>
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
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-900">Payment received — thank you!</h1>
                <p class="mt-1 text-sm text-ink-500">
                    Order <span class="font-mono font-semibold text-ink-800"><?php echo e($order->order_number); ?></span> is confirmed.
                    We will call <?php echo e($order->phone); ?> before delivery.
                </p>
            <?php elseif($order->status === \App\Enums\OrderStatus::Cancelled): ?>
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-ink-100 text-ink-500">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'x','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x','class' => 'size-7']); ?>
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
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-900">Order cancelled</h1>
                <p class="mt-1 text-sm text-ink-500">
                    Order <span class="font-mono font-semibold text-ink-800"><?php echo e($order->order_number); ?></span> was cancelled. Nothing was charged.
                </p>
            <?php else: ?>
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-600/20">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'clock','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','class' => 'size-7']); ?>
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
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-900">Complete your payment</h1>
                <p class="mt-1 text-sm text-ink-500">
                    Order <span class="font-mono font-semibold text-ink-800"><?php echo e($order->order_number); ?></span>
                    is waiting for <?php echo e(strtolower($order->payment_gateway?->label() ?? 'payment')); ?>.
                </p>
            <?php endif; ?>
        </div>

        <?php if(isset($gatewayError)): ?>
            <div class="mb-5 rounded-lg bg-amber-50 p-4 text-sm text-amber-800 ring-1 ring-amber-600/20">
                <p class="font-semibold">Payment could not be started</p>
                <p class="mt-1"><?php echo e($gatewayError); ?></p>
            </div>
        <?php endif; ?>

        <?php if($order->isPending()): ?>
            <section class="card mb-5 overflow-hidden">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Pay <?php echo e($summary_total = \App\Support\Money::format($order->total)); ?></h2>
                        <p class="text-xs text-ink-400">Reference <?php echo e($order->payment_reference ?? $order->order_number); ?></p>
                    </div>
                    <span class="badge <?php echo e($order->payment_gateway?->tone()); ?>"><?php echo e($order->payment_gateway?->label()); ?></span>
                </div>

                <div class="card-body">
                    <?php if($intent && $intent['redirect_url']): ?>
                        <?php if($order->payment_gateway === \App\Enums\PaymentGateway::Sandbox): ?>
                            <a href="<?php echo e($intent['redirect_url']); ?>" class="btn btn-primary btn-lg w-full">
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
                                Continue to the payment page
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e($intent['redirect_url']); ?>" class="btn btn-primary btn-lg w-full" rel="noopener">
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
                                Pay with <?php echo e($order->payment_gateway->label()); ?>

                            </a>
                        <?php endif; ?>
                    <?php elseif($order->payment_gateway === \App\Enums\PaymentGateway::Sandbox): ?>
                        <a href="<?php echo e(route('payments.sandbox', $order)); ?>" class="btn btn-primary btn-lg w-full">
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
<?php endif; ?> Open the sandbox payment page
                        </a>
                    <?php elseif($order->payment_gateway === \App\Enums\PaymentGateway::Cash): ?>
                        <p class="rounded-lg bg-emerald-50 p-4 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                            Pay the rider in cash when your goods arrive. Nothing more to do — we will call
                            <?php echo e($order->phone); ?> to confirm.
                        </p>
                    <?php endif; ?>

                    <?php if($intent && ! empty($intent['instructions'])): ?>
                        <ul class="mt-4 space-y-2">
                            <?php $__currentLoopData = $intent['instructions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-start gap-2 text-xs text-ink-500">
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','class' => 'mt-px size-3.5 shrink-0 text-brand-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'mt-px size-3.5 shrink-0 text-brand-600']); ?>
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
                                    <span><?php echo e($line); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('checkout.cancel', $order)); ?>" class="mt-5 border-t border-ink-100 pt-4"
                          onsubmit="return confirm('Cancel this order?')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                            Cancel this order
                        </button>
                    </form>
                </div>
            </section>
        <?php endif; ?>

        <div class="grid items-start gap-5 sm:grid-cols-2">
            <section class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="card-title">Items</h2>
                </div>
                <ul class="divide-y divide-ink-100">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                            <span class="grid size-8 shrink-0 place-items-center rounded-md bg-brand-50 text-xs font-bold text-brand-700 tabular-nums">
                                <?php echo e($item->quantity); ?>

                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-medium text-ink-800"><?php echo e($item->name); ?></p>
                                <p class="font-mono text-[0.6875rem] text-ink-400"><?php echo e($item->sku); ?></p>
                                
                                <p class="text-[0.6875rem] text-ink-500 tabular-nums">
                                    <?php echo e($item->unitPriceFormatted()); ?> &times; <?php echo e($item->quantity); ?> <?php echo e($item->unit); ?>

                                </p>
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
                            </div>
                            <span class="shrink-0 text-sm font-bold text-ink-900 tabular-nums">
                                <?php echo e($item->lineTotalFormatted()); ?>

                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
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
                    <h2 class="card-title">Delivery &amp; payment</h2>
                </div>
                <div class="card-body space-y-3 text-sm">
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Deliver to</p>
                        <p class="mt-0.5 font-medium text-ink-900"><?php echo e($order->customer_name); ?></p>
                        <p class="text-ink-600"><?php echo e($order->delivery_address); ?></p>
                        <p class="text-ink-600"><?php echo e($order->township); ?></p>
                    </div>
                    <div class="border-t border-ink-100 pt-3">
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Contact</p>
                        <p class="mt-0.5 text-ink-700"><?php echo e($order->phone); ?></p>
                        <p class="text-ink-600"><?php echo e($order->email); ?></p>
                    </div>
                    <?php if($order->note): ?>
                        <div class="border-t border-ink-100 pt-3">
                            <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Note</p>
                            <p class="mt-0.5 text-ink-600"><?php echo e($order->note); ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="border-t border-ink-100 pt-3">
                        <p class="text-[0.6875rem] font-semibold tracking-wide text-ink-400 uppercase">Status</p>
                        <div class="mt-1.5 flex flex-wrap gap-2">
                            <span class="badge <?php echo e($order->status->tone()); ?>"><?php echo e($order->status->label()); ?></span>
                            <?php if($order->payment_gateway): ?>
                                <span class="badge <?php echo e($order->payment_gateway->tone()); ?>"><?php echo e($order->payment_gateway->label()); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if($order->paid_at): ?>
                            <p class="mt-2 text-xs text-ink-500">Paid <?php echo e($order->paid_at->diffForHumans()); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-6 flex justify-center">
            <a href="<?php echo e(route('catalog.index')); ?>" class="btn btn-secondary">Continue shopping</a>
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
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/checkout/show.blade.php ENDPATH**/ ?>