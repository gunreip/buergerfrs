{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/order-lifecycle.blade.php --}}

<x-layouts::app :title="__('TW-Graph Sample: Order Lifecycle')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('Order Lifecycle')"
            :description="__(
                'Hand-authored tw-graph sample for a commerce order flow with payment, fulfillment, refund, and return side paths.',
            )"
        />

        @php
            $orderLifecycleGraphId = 'tw-graph-sample-order-lifecycle';
            $orderLifecycleDev = true;
            $orderLifecycleCoordinates = false;
        @endphp

        <div x-data="{ orderLifecycleDev: @js($orderLifecycleDev) }">
            <flux:callout
                class="mt-6"
                color="zinc"
                icon="package-check"
            >
                <flux:callout.heading>
                    <span class="flex w-full flex-wrap items-center justify-between gap-3">
                        <span class="inline-flex flex-wrap items-center gap-2">
                            <span>{{ __('Order lifecycle graph canvas') }}</span>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >
                                {{ $orderLifecycleGraphId }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="amber"
                            >
                                {{ __('hand-authored') }}
                            </flux:badge>
                        </span>
                        @if ($orderLifecycleDev)
                            <flux:field
                                class="items-center gap-2"
                                variant="inline"
                                x-on:click.stop
                            >
                                <flux:switch
                                    class="switch-colored hover:cursor-pointer"
                                    x-bind:checked="orderLifecycleDev"
                                    x-on:click="orderLifecycleDev = !orderLifecycleDev"
                                />
                                <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                                    {{ __('DEV') }}
                                </flux:label>
                            </flux:field>
                        @endif
                    </span>
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('Manual process sample showing the happy path from cart to delivery, with visible exception paths for failed payment, refund handling, and returns.') }}
                </flux:callout.text>

                <div
                    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !orderLifecycleDev }"
                >
                    <x-translation-workbench::ui.tw-graph
                        class="px-28 py-14"
                        :graph-id="$orderLifecycleGraphId"
                        :dev="$orderLifecycleDev"
                        :coordinates="$orderLifecycleCoordinates"
                        color="emerald"
                        line-length="4rem"
                        bridge-length="16rem"
                        stem-length="5rem"
                        slot-min-height="86rem"
                        horizontal-padding="38rem"
                    >
                        {{-- happy path --}}
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="order.center.1.lifecycle"
                            :stem-count="22"
                            start-length="4rem"
                            :stem-lengths="[
                                1 => '5rem',
                                2 => '3rem',
                                3 => '3rem',
                                4 => '5rem',
                                5 => '3rem',
                                6 => '3.9rem',
                                7 => '4.9rem',
                                8 => '4.9rem',
                                9 => '7.0rem',
                                10 => '8.0rem',
                                11 => '7.0rem',
                                12 => '7.0rem',
                                14 => '4.0rem',
                                15 => '4.0rem',
                                16 => '4.0rem',
                                17 => '4.0rem',
                                18 => '4.0rem',
                                19 => '4.0rem',
                                19 => '4.0rem',
                                20 => '4.0rem',
                                21 => '4.0rem',
                                22 => '4.0rem',
                            ]"
                            end-length="4rem"
                            :start-label="[
                                'text' => ['Order lifecycle', 'commerce flow'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                            :end-label="[
                                'text' => ['Delivery complete', 'order closed'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                            :start-node-labels="[
                                'left' => [
                                    'text' => ['Cart', 'items reserved'],
                                    'width' => 'default',
                                    'align' => 'left',
                                    'color' => 'emerald',
                                ],
                                'right' => [
                                    'text' => [
                                        'Customer starts an order and the cart becomes the first stable process state.',
                                    ],
                                    'width' => 'long',
                                    'align' => 'left',
                                    'justify' => true,
                                    'color' => 'sky',
                                ],
                            ]"
                            :node-labels="[
                                1 => [
                                    'right' => [
                                        'text' => ['Checkout', 'Confirmation-eMail send'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'color' => 'emerald',
                                    ],
                                ],
                                2 => [
                                    'left' => [
                                        'text' => ['Checkout', 'Confirmation-eMail received'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                ],
                                3 => [
                                    'right' => [
                                        'text' => ['Checkout', 'Delivery Slip send by eMail'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'color' => 'emerald',
                                    ],
                                ],
                                4 => [
                                    'right' => [
                                        'text' => ['Checkout', 'Rechnung send by eMail'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'color' => 'emerald',
                                    ],
                                ],
                                5 => [
                                    'left' => [
                                        'text' => ['Checkout', 'address and shipping'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                ],
                                6 => [
                                    'left' => [
                                        'text' => ['Payment', 'authorization requested'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'amber',
                                    ],
                                    'right' => [
                                        'text' => [
                                            'Payment gateway receives the authorization request and returns a decision.',
                                        ],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                7 => [
                                    'right' => [
                                        'text' => [
                                            'Shipping method, tax estimate, and delivery promise are calculated.',
                                        ],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                8 => [
                                    'left' => [
                                        'text' => ['Fulfillment', 'warehouse pick list'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                    'right' => [
                                        'text' => ['Inventory', 'committed'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                9 => [
                                    'right' => [
                                        'text' => ['Inventory', 'picked'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                10 => [
                                    'right' => [
                                        'text' => ['Inventory', 'packed'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                11 => [
                                    'left' => [
                                        'text' => ['Delivery', 'carrier confirmed'],
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                ],
                                13 => [
                                    'left' => [
                                        'text' => ['Inventory', 'handed to the carrier'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'emerald',
                                    ],
                                ],
                                14 => [
                                    'right' => [
                                        'text' => ['Shipment', 'receives the sending station.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                15 => [
                                    'right' => [
                                        'text' => ['Shipment', 'leaves the sending station to HUB.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                16 => [
                                    'right' => [
                                        'text' => ['Shipment', 'receives at the HUB.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                17 => [
                                    'right' => [
                                        'text' => ['Shipment', 'sorted at the HUB for destination.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                18 => [
                                    'right' => [
                                        'text' => ['Shipment', 'leaves the HUB to the destination station.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                19 => [
                                    'right' => [
                                        'text' => ['Shipment', 'arrives at the destination station.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                20 => [
                                    'right' => [
                                        'text' => ['Shipment', 'sorted at the destination station.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                21 => [
                                    'right' => [
                                        'text' => ['Shipment', 'loaded at the destination station.'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                    'left' => [
                                        'text' => ['Customer', 'receives the parcel.'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'justify' => true,
                                        'color' => 'emerald',
                                    ],
                                ],
                                22 => [
                                    'right' => [
                                        'text' => ['Tracking is closed after the customer receives the parcel.'],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'red',
                                    ],
                                ],
                            ]"
                        />

                        {{-- failed payment branch --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-left
                            id="order.left.1.payment-failed"
                            attach-to="strang.trunk.node.7"
                            color="rose"
                            entry-stem-length="0.5rem"
                            bridge-length="44rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'left' => [
                                        'text' => ['Payment failed', 'issuer declined'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '2rem',
                                'afterLength' => '2rem',
                                'stepLabel' => [
                                    'text' => ['Decision', 'retry or cancel'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '4rem',
                                    'left' => [
                                        'text' => ['Retry link', 'customer notified'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                                2 => [
                                    '4rem',
                                    'left' => [
                                        'text' => ['Order held', 'reservation expires'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                            :branch-return="[
                                1 => [
                                    'attachTo' => 'stem.1',
                                    'bridgeLength' => '44rem',
                                    'color' => 'rose',
                                    'fallback' => false,
                                ],
                            ]"
                        />

                        {{-- payment recovery merges back into fulfillment --}}
                        <x-translation-workbench::ui.tw-graph.strang.merge-left
                            id="order.left.1.payment-recovered"
                            attach-to="strang.trunk.node.10"
                            color="amber"
                            bridge-length="28rem"
                            :stem-lengths="[1 => '0rem']"
                            :start-label="[
                                'text' => ['Payment recovered', 'authorization captured'],
                                'width' => 'halfLong',
                                'align' => 'center',
                                'color' => 'amber',
                            ]"
                            :node-labels="[
                                1 => [
                                    'right' => [
                                        'text' => ['Retry succeeds', 'new card accepted'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                5 => [
                                    'left' => [
                                        'text' => ['continue', 'fulfillment'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                        />

                        {{-- return request after delivery --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-right
                            id="order.right.1.return-request"
                            attach-to="strang.trunk.node.13"
                            color="sky"
                            entry-stem-length="0.5rem"
                            bridge-length="24rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'right' => [
                                        'text' => ['Return requested', 'customer opens RMA'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '4rem',
                                'afterLength' => '4rem',
                                'stepLabel' => [
                                    'text' => ['Inspection', 'refund decision'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '4rem',
                                    'right' => [
                                        'text' => ['Item received', 'condition checked'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                2 => [
                                    '5rem',
                                    'right' => [
                                        'text' => ['Refund issued', 'payment reversed'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                        />

                        <x-translation-workbench::ui.tw-graph.strang.branch-end
                            id="order.right.1.return-request.end"
                            side="right"
                            attach-to="strang.branch-right.end"
                            color="sky"
                            length="3rem"
                            :end-label="[
                                'text' => ['Return closed', 'refund completed'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />

                        {{-- fulfillment exception path --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-right
                            id="order.right.2.fulfillment-delay"
                            attach-to="strang.trunk.node.10"
                            color="violet"
                            entry-stem-length="0.5rem"
                            bridge-length="24rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'right' => [
                                        'text' => ['Fulfillment delay', 'stock mismatch'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '2.7rem',
                                'afterLength' => '2.6rem',
                                'stepLabel' => [
                                    'text' => ['Decision', 'split shipment'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '4rem',
                                    'right' => [
                                        'text' => ['Backorder', 'partial shipment sent'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                            :branch-return="[
                                1 => [
                                    'attachTo' => 'stem.1',
                                    'bridgeLength' => '24rem',
                                    'color' => 'violet',
                                    'fallback' => false,
                                ],
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                </div>
            </flux:callout>
        </div>
    </flux:card>
</x-layouts::app>
