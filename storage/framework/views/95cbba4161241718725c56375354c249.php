<?php if (isset($component)) { $__componentOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c0e86a062c1c5bb6d0e151b7076f3fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.public','data' => ['title' => 'Checkout','description' => 'Delivery details and payment']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Checkout','description' => 'Delivery details and payment']); ?>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        <h1 class="mb-5 text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Checkout</h1>

        <form method="POST" action="<?php echo e(route('checkout.store')); ?>" class="grid items-start gap-5 lg:grid-cols-3">
            <?php echo csrf_field(); ?>

            <div class="space-y-5 lg:col-span-2">
                <section class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Delivery details</h2>
                            <p class="text-xs text-ink-400">Where should we bring your goods?</p>
                        </div>
                    </div>
                    <div class="card-body grid gap-4 sm:grid-cols-2">
                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'customer_name','label' => 'Full name','value' => old('customer_name', $user?->name),'required' => true,'placeholder' => 'Aung Kyaw']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'customer_name','label' => 'Full name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('customer_name', $user?->name)),'required' => true,'placeholder' => 'Aung Kyaw']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'phone','label' => 'Mobile number','value' => old('phone', $user?->phone),'required' => true,'placeholder' => '09 380 000 00']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'phone','label' => 'Mobile number','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('phone', $user?->phone)),'required' => true,'placeholder' => '09 380 000 00']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>

                        <div class="sm:col-span-2">
                            <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'email','label' => 'Email address','type' => 'email','value' => old('email', $user?->email),'required' => true,'placeholder' => 'you@example.com']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'email','label' => 'Email address','type' => 'email','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('email', $user?->email)),'required' => true,'placeholder' => 'you@example.com']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                        </div>

                        <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'township','label' => 'Township','type' => 'select','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'township','label' => 'Township','type' => 'select','required' => true]); ?>
                            <option value="">Choose a township…</option>
                            <?php $__currentLoopData = $townships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $township): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($township); ?>" <?php if(old('township') === $township): echo 'selected'; endif; ?>>
                                    <?php echo e($township); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>

                        <div class="sm:col-span-2" data-delivery-quote hidden>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 rounded-lg border border-brand-200 bg-brand-50 p-3 text-sm text-brand-900">
                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'truck','class' => 'size-4 shrink-0 text-brand-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'truck','class' => 'size-4 shrink-0 text-brand-600']); ?>
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
                                <span data-delivery-zone class="font-semibold"></span>
                                <span class="text-brand-700">·</span>
                                <span data-delivery-eta class="text-brand-800"></span>
                                <span class="ms-auto font-bold tabular-nums" data-delivery-fee></span>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'delivery_address','label' => 'Street address','type' => 'textarea','rows' => 2,'value' => old('delivery_address'),'required' => true,'placeholder' => 'No. 12, Baho Road, Kamayut']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'delivery_address','label' => 'Street address','type' => 'textarea','rows' => 2,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('delivery_address')),'required' => true,'placeholder' => 'No. 12, Baho Road, Kamayut']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                        </div>

                        <div class="sm:col-span-2">
                            <?php if (isset($component)) { $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-field','data' => ['field' => 'note','label' => 'Note for the rider','type' => 'textarea','rows' => 2,'value' => old('note'),'placeholder' => 'Call when you arrive, deliver to the 3rd floor…']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => 'note','label' => 'Note for the rider','type' => 'textarea','rows' => 2,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('note')),'placeholder' => 'Call when you arrive, deliver to the 3rd floor…']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $attributes = $__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__attributesOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d)): ?>
<?php $component = $__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d; ?>
<?php unset($__componentOriginalf4c8ecf26ef77d4de25edf56eae3a34d); ?>
<?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Payment method</h2>
                            <p class="text-xs text-ink-400">Myanmar online banking and wallets</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-ink-200 p-3.5 transition
                                              hover:border-brand-400 hover:bg-brand-50/40
                                              has-checked:border-brand-500 has-checked:bg-brand-50">
                                    <input type="radio" name="payment_gateway" value="<?php echo e($gateway->value); ?>"
                                           class="checkbox mt-0.5" <?php if(old('payment_gateway', $gateways->first()?->value) === $gateway->value): echo 'checked'; endif; ?>>

                                    <span class="min-w-0 flex-1">
                                        <span class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-semibold text-ink-900"><?php echo e($gateway->label()); ?></span>
                                            <?php if($gateway === \App\Enums\PaymentGateway::Sandbox): ?>
                                                <span class="badge bg-ink-100 text-ink-600 ring-ink-500/10">Test mode</span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="mt-0.5 block text-xs leading-relaxed text-ink-500"><?php echo e($gateway->blurb()); ?></span>
                                    </span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php $__errorArgs = ['payment_gateway'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="help-error"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','class' => 'size-3.5 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','class' => 'size-3.5 shrink-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </section>
            </div>

            <aside class="card lg:sticky lg:top-36">
                <div class="card-header">
                    <h2 class="card-title">Your order</h2>
                    <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-ghost btn-sm">Edit</a>
                </div>

                <ul class="max-h-64 divide-y divide-ink-100 overflow-y-auto">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-3 px-4 py-2.5 sm:px-5">
                            <span class="grid size-8 shrink-0 place-items-center rounded-md bg-brand-50 text-xs font-bold text-brand-700 tabular-nums">
                                <?php echo e($item['quantity']); ?>

                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-medium text-ink-800"><?php echo e($item['product']->name); ?></p>
                                <?php if($item['product']->hasDiscount()): ?>
                                    <p class="text-[0.6875rem] tabular-nums">
                                        <s class="text-rose-600"><?php echo e(\App\Support\Money::format($item['product']->price)); ?></s>
                                        <span class="ms-1 text-blue-700"><?php echo e(\App\Support\Money::format($item['product']->effectivePrice())); ?></span>
                                    </p>
                                <?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal55a2a145c73971d8b3899d9083ae76a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55a2a145c73971d8b3899d9083ae76a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.freshness','data' => ['product' => $item['product'],'variant' => 'compact']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('freshness'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['product']),'variant' => 'compact']); ?>
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
                            <span class="shrink-0 text-xs font-semibold text-blue-700 tabular-nums">
                                <?php echo e(\App\Support\Money::format($item['line_total'])); ?>

                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                <div class="card-body space-y-3 border-t border-ink-200"
                     data-delivery-calculator
                     data-subtotal="<?php echo e($summary['subtotal']); ?>"
                     data-currency="<?php echo e(\App\Support\Money::symbol()); ?>"
                     data-map='<?php echo json_encode($townshipMap, 15, 512) ?>'
                     data-zones='<?php echo json_encode(collect($zones)->pluck("label", "key"), 512) ?>'>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-600">Subtotal</span>
                        <span class="font-semibold text-ink-900 tabular-nums"><?php echo e($summary['subtotal_formatted']); ?></span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-600">Delivery</span>
                        <span class="font-semibold text-ink-900 tabular-nums" data-delivery-summary>
                            <?php echo e($summary['delivery_range']['min_formatted']); ?> – <?php echo e($summary['delivery_range']['max_formatted']); ?>

                        </span>
                    </div>

                    <div class="flex items-center justify-between border-t border-ink-200 pt-3">
                        <span class="text-sm font-semibold text-ink-900">Total to pay</span>
                        <span class="text-lg font-bold text-blue-700 tabular-nums" data-order-total>
                            <?php echo e(\App\Support\Money::format($summary['subtotal'] + $summary['delivery_range']['min'])); ?>

                        </span>
                    </div>

                    <p class="text-[0.6875rem] text-ink-400">
                        Delivery is quoted from your township at checkout.
                    </p>

                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'shield','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield','class' => 'size-4']); ?>
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
                        Place order
                    </button>

                    <p class="text-center text-[0.6875rem] text-ink-400">
                        All prices in <?php echo e(\App\Support\Money::code()); ?> (Myanmar kyat).
                    </p>
                </div>
            </aside>
        </form>
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
<?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/checkout/create.blade.php ENDPATH**/ ?>